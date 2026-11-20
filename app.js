const CATEGORY_LABELS = {
  rice: "Cơm",
  noodles: "Bún & mì",
  soup: "Món nước",
  snack: "Ăn vặt",
  vegetarian: "Món chay",
};

const CATEGORY_EMOJIS = {
  rice: "🍚",
  noodles: "🍜",
  soup: "🥣",
  snack: "🥟",
  vegetarian: "🥬",
};

const DEFAULT_MEALS = [
  { id: "com-tam", name: "Cơm tấm sườn", category: "rice", emoji: "🍖", description: "Sườn nướng thơm, thêm chút mỡ hành là chuẩn bài." },
  { id: "com-ga", name: "Cơm gà", category: "rice", emoji: "🍗", description: "Đĩa cơm vàng thơm, gà mềm và nước chấm đậm đà." },
  { id: "com-rang", name: "Cơm rang trứng", category: "rice", emoji: "🍳", description: "Nhanh gọn, nóng hổi và lúc nào cũng hợp." },
  { id: "bun-cha", name: "Bún chả", category: "noodles", emoji: "🥢", description: "Thịt nướng thơm lừng ăn cùng bún và rau tươi." },
  { id: "bun-bo", name: "Bún bò Huế", category: "noodles", emoji: "🌶️", description: "Một tô cay thơm, đậm vị cho chiếc bụng đói." },
  { id: "mi-xao", name: "Mì xào giòn", category: "noodles", emoji: "🍝", description: "Sợi mì giòn rụm, rau củ và sốt sánh đậm." },
  { id: "pho-bo", name: "Phở bò", category: "soup", emoji: "🍲", description: "Nước dùng thơm, bánh phở mềm và thật nhiều hành." },
  { id: "hu-tieu", name: "Hủ tiếu", category: "soup", emoji: "🍜", description: "Tô hủ tiếu nhẹ nhàng, thêm tôm thịt tùy thích." },
  { id: "banh-xeo", name: "Bánh xèo", category: "snack", emoji: "🥞", description: "Vỏ bánh vàng giòn cuốn rau sống, chấm nước mắm." },
  { id: "goi-cuon", name: "Gỏi cuốn", category: "snack", emoji: "🥬", description: "Thanh mát, tôm thịt vừa đủ, chấm tương đậu." },
  { id: "banh-mi", name: "Bánh mì", category: "snack", emoji: "🥖", description: "Ổ bánh giòn rụm cho bữa ăn nhanh mà ngon." },
  { id: "dau-hu", name: "Đậu hũ sốt cà", category: "vegetarian", emoji: "🍅", description: "Món chay giản dị, mềm thơm và rất đưa cơm." },
];

const CUSTOM_MEALS_KEY = "randomfood.customMeals";
const elements = {
  addForm: document.querySelector("#add-form"),
  clearCustomButton: document.querySelector("#clear-custom-button"),
  emptyState: document.querySelector("#empty-state"),
  filters: document.querySelector(".filters"),
  mealCategory: document.querySelector("#meal-category"),
  mealGrid: document.querySelector("#meal-grid"),
  mealName: document.querySelector("#meal-name"),
  mealTotal: document.querySelector("#meal-total"),
  pickButton: document.querySelector("#pick-button"),
  resultCategory: document.querySelector("#result-category"),
  resultContent: document.querySelector("#result-content"),
  resultCount: document.querySelector("#result-count"),
  resultDescription: document.querySelector("#result-description"),
  resultEmoji: document.querySelector("#result-emoji"),
  resultTitle: document.querySelector("#result-title"),
  rerollButton: document.querySelector("#reroll-button"),
  searchInput: document.querySelector("#search-input"),
  storageNotice: document.querySelector("#storage-notice"),
};

let customMeals = loadCustomMeals();
let activeCategory = "all";
let selectedMealId = null;
let recentlyPicked = [];

function loadCustomMeals() {
  try {
    const savedMeals = window.localStorage.getItem(CUSTOM_MEALS_KEY);
    if (!savedMeals) return [];

    const parsedMeals = JSON.parse(savedMeals);
    if (!Array.isArray(parsedMeals)) throw new Error("Dữ liệu món ăn không hợp lệ.");

    return parsedMeals.filter(isValidCustomMeal);
  } catch (error) {
    showStorageNotice("Không đọc được danh sách món đã lưu trên trình duyệt này.");
    console.error("Could not load saved meals:", error);
    return [];
  }
}

function isValidCustomMeal(meal) {
  return Boolean(
    meal
    && typeof meal.id === "string"
    && typeof meal.name === "string"
    && Object.hasOwn(CATEGORY_LABELS, meal.category)
    && typeof meal.emoji === "string"
    && typeof meal.description === "string",
  );
}

function getAllMeals() {
  return [...DEFAULT_MEALS, ...customMeals];
}

function showStorageNotice(message) {
  elements.storageNotice.textContent = message;
  elements.storageNotice.hidden = false;
}

function saveCustomMeals() {
  try {
    window.localStorage.setItem(CUSTOM_MEALS_KEY, JSON.stringify(customMeals));
    elements.storageNotice.hidden = true;
    return true;
  } catch (error) {
    showStorageNotice("Món đã thêm nhưng trình duyệt không thể lưu để dùng lần sau.");
    console.error("Could not save meals:", error);
    return false;
  }
}

function getVisibleMeals() {
  const query = elements.searchInput.value.trim().toLocaleLowerCase("vi");

  return getAllMeals().filter((meal) => {
    const matchesCategory = activeCategory === "all" || meal.category === activeCategory;
    const matchesSearch = !query || meal.name.toLocaleLowerCase("vi").includes(query);
    return matchesCategory && matchesSearch;
  });
}

function renderMeals() {
  const visibleMeals = getVisibleMeals();
  elements.mealGrid.replaceChildren();
  elements.mealTotal.textContent = String(visibleMeals.length);
  elements.emptyState.hidden = visibleMeals.length > 0;

  for (const meal of visibleMeals) {
    const tile = document.createElement("button");
    tile.type = "button";
    tile.className = "meal-tile";
    tile.setAttribute("aria-label", `Chọn món ${meal.name}`);

    const emoji = document.createElement("span");
    emoji.className = "meal-emoji";
    emoji.setAttribute("aria-hidden", "true");
    emoji.textContent = meal.emoji;

    const details = document.createElement("span");
    details.className = "meal-details";

    const name = document.createElement("span");
    name.className = "meal-name";
    name.textContent = meal.name;

    const category = document.createElement("span");
    category.className = "meal-category";
    category.textContent = CATEGORY_LABELS[meal.category];

    details.append(name, category);
    tile.append(emoji, details);
    tile.addEventListener("click", () => showMeal(meal));
    elements.mealGrid.append(tile);
  }
}

function showMeal(meal) {
  selectedMealId = meal.id;
  elements.resultEmoji.textContent = meal.emoji;
  elements.resultCategory.textContent = CATEGORY_LABELS[meal.category].toLocaleUpperCase("vi");
  elements.resultTitle.textContent = meal.name;
  elements.resultDescription.textContent = meal.description;

  const visibleMeals = getVisibleMeals();
  const selectedPosition = visibleMeals.findIndex((item) => item.id === meal.id);
  elements.resultCount.textContent = selectedPosition >= 0
    ? `${String(selectedPosition + 1).padStart(2, "0")} / ${String(visibleMeals.length).padStart(2, "0")}`
    : "✦ / ✦";
}

function pickMeal() {
  const candidates = getVisibleMeals();
  if (candidates.length === 0) {
    elements.resultTitle.textContent = "Chưa có món phù hợp";
    elements.resultCategory.textContent = "THỬ LẠI NHÉ";
    elements.resultDescription.textContent = "Hãy đổi bộ lọc hoặc thêm món mới vào danh sách.";
    elements.resultEmoji.textContent = "🔎";
    elements.resultCount.textContent = "00 / 00";
    return;
  }

  let availableMeals = candidates.filter((meal) => !recentlyPicked.includes(meal.id));
  if (availableMeals.length === 0) availableMeals = candidates;

  const meal = availableMeals[Math.floor(Math.random() * availableMeals.length)];
  recentlyPicked = [...recentlyPicked, meal.id].slice(-Math.max(1, candidates.length - 1));
  elements.resultContent.classList.add("is-changing");
  window.setTimeout(() => elements.resultContent.classList.remove("is-changing"), 180);
  showMeal(meal);
}

elements.filters.addEventListener("click", (event) => {
  const button = event.target.closest("[data-category]");
  if (!button) return;

  activeCategory = button.dataset.category;
  for (const filter of elements.filters.querySelectorAll("[data-category]")) {
    const isActive = filter === button;
    filter.classList.toggle("is-active", isActive);
    filter.setAttribute("aria-pressed", String(isActive));
  }
  renderMeals();
});

elements.searchInput.addEventListener("input", renderMeals);
elements.pickButton.addEventListener("click", pickMeal);
elements.rerollButton.addEventListener("click", pickMeal);

elements.addForm.addEventListener("submit", (event) => {
  event.preventDefault();
  const name = elements.mealName.value.trim();
  if (!name) {
    elements.mealName.focus();
    return;
  }

  const duplicate = getAllMeals().some((meal) => meal.name.toLocaleLowerCase("vi") === name.toLocaleLowerCase("vi"));
  if (duplicate) {
    showStorageNotice("Món này đã có trong danh sách rồi.");
    elements.mealName.focus();
    return;
  }

  const category = elements.mealCategory.value;
  const meal = {
    id: `custom-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`,
    name,
    category,
    emoji: CATEGORY_EMOJIS[category],
    description: `Món ${CATEGORY_LABELS[category].toLocaleLowerCase("vi")} do bạn thêm vào danh sách.`,
  };

  customMeals = [...customMeals, meal];
  saveCustomMeals();
  elements.addForm.reset();
  activeCategory = "all";
  for (const filter of elements.filters.querySelectorAll("[data-category]")) {
    const isActive = filter.dataset.category === "all";
    filter.classList.toggle("is-active", isActive);
    filter.setAttribute("aria-pressed", String(isActive));
  }
  elements.searchInput.value = "";
  renderMeals();
  showMeal(meal);
});

elements.clearCustomButton.addEventListener("click", () => {
  if (customMeals.length === 0) {
    showStorageNotice("Danh sách chưa có món nào do bạn thêm.");
    return;
  }

  customMeals = [];
  saveCustomMeals();
  if (customMeals.length === 0) {
    try {
      window.localStorage.removeItem(CUSTOM_MEALS_KEY);
    } catch (error) {
      showStorageNotice("Không thể xóa dữ liệu đã lưu trong trình duyệt.");
      console.error("Could not clear saved meals:", error);
    }
  }
  recentlyPicked = [];
  renderMeals();
  showStorageNotice("Đã xóa các món bạn thêm.");
});

renderMeals();

SET NAMES utf8mb4;

START TRANSACTION;

INSERT IGNORE INTO categories (id, name, emoji) VALUES
    ('soup', 'Món nước', '🥣'),
    ('noodles', 'Bún & mì', '🍜'),
    ('rice', 'Cơm', '🍚'),
    ('snack', 'Ăn nhẹ', '🥟'),
    ('vegetarian', 'Món chay', '🥬');

INSERT IGNORE INTO meals (id, category_id, name, emoji, rating, review_count, price, location, prep_time, description, image, is_seed) VALUES
    ('pho-bo-ha-noi', 'soup', 'Phở bò Hà Nội', '🍲', '4.9', '128', '45.000–65.000đ', 'Miền Bắc', '30 phút', 'Nước dùng trong, thơm dịu, bánh phở mềm và thịt bò tươi. Một tô phở nóng hổi cho ngày mới đầy năng lượng.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('bun-bo-hue', 'noodles', 'Bún bò Huế', '🌶️', '4.8', '96', '40.000–60.000đ', 'Miền Trung', '35 phút', 'Nước lèo đậm đà vị sả và mắm ruốc, thêm chút cay nồng đặc trưng của xứ Huế.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('com-tam-suon', 'rice', 'Cơm tấm sườn nướng', '🍖', '4.8', '84', '35.000–55.000đ', 'Miền Nam', '25 phút', 'Sườn nướng thơm, cơm tấm dẻo tơi, ăn cùng mỡ hành và nước mắm chua ngọt.', 'https://images.unsplash.com/photo-1516684732162-798a0062be99?auto=format&fit=crop&w=1200&q=85', '1'),
    ('bun-cha-ha-noi', 'noodles', 'Bún chả Hà Nội', '🥢', '4.7', '72', '40.000–60.000đ', 'Miền Bắc', '30 phút', 'Thịt nướng xém cạnh, nước chấm chua ngọt và rau sống tươi mát.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=1200&q=85', '1'),
    ('banh-mi-thit', 'snack', 'Bánh mì thịt', '🥖', '4.7', '63', '20.000–35.000đ', 'Miền Nam', '10 phút', 'Vỏ bánh giòn rụm, nhân thịt đậm vị, rau thơm và đồ chua cân bằng.', 'https://images.unsplash.com/photo-1600688640154-9619e002df30?auto=format&fit=crop&w=1200&q=85', '1'),
    ('goi-cuon', 'snack', 'Gỏi cuốn tôm thịt', '🥬', '4.6', '51', '30.000–45.000đ', 'Miền Nam', '20 phút', 'Cuốn tươi thanh mát với tôm, thịt, bún và rau sống; chấm tương đậu.', 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=85', '1'),
    ('mi-quang', 'noodles', 'Mì Quảng', '🍜', '4.8', '58', '35.000–55.000đ', 'Miền Trung', '30 phút', 'Sợi mì vàng, nước dùng sánh nhẹ, đậu phộng rang và bánh tráng giòn.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('com-ga-hoi-an', 'rice', 'Cơm gà Hội An', '🍗', '4.8', '69', '40.000–60.000đ', 'Miền Trung', '30 phút', 'Cơm nấu nước luộc gà vàng thơm, thịt gà xé và rau thơm.', 'https://images.unsplash.com/photo-1516684732162-798a0062be99?auto=format&fit=crop&w=1200&q=85', '1'),
    ('bun-rieu', 'soup', 'Bún riêu cua', '🦀', '4.7', '42', '35.000–50.000đ', 'Miền Bắc', '35 phút', 'Nước dùng chua thanh, riêu cua béo thơm và rau sống ăn kèm.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('hu-tieu-nam-vang', 'soup', 'Hủ tiếu Nam Vang', '🍜', '4.7', '48', '35.000–55.000đ', 'Miền Nam', '25 phút', 'Sợi hủ tiếu mềm dai, nước dùng thanh ngọt cùng tôm và thịt.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('banh-xeo', 'snack', 'Bánh xèo miền Tây', '🥞', '4.6', '39', '35.000–60.000đ', 'Miền Nam', '25 phút', 'Vỏ bánh vàng giòn, nhân tôm thịt và thật nhiều rau xanh.', 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=1200&q=85', '1'),
    ('bun-dau-mam-tom', 'noodles', 'Bún đậu mắm tôm', '🍱', '4.8', '91', '40.000–70.000đ', 'Miền Bắc', '20 phút', 'Đậu rán vàng, bún lá, chả cốm và mắm tôm đánh bông.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=1200&q=85', '1'),
    ('com-rang-dua-bo', 'rice', 'Cơm rang dưa bò', '🍳', '4.6', '34', '35.000–50.000đ', 'Miền Bắc', '20 phút', 'Cơm rang săn hạt ăn cùng thịt bò xào dưa chua đậm đà.', 'https://images.unsplash.com/photo-1516684732162-798a0062be99?auto=format&fit=crop&w=1200&q=85', '1'),
    ('banh-cuon', 'snack', 'Bánh cuốn nóng', '🥟', '4.6', '32', '25.000–40.000đ', 'Miền Bắc', '20 phút', 'Lớp bánh mỏng mềm, nhân thịt mộc nhĩ, hành phi thơm.', 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=1200&q=85', '1'),
    ('cao-lau', 'noodles', 'Cao lầu Hội An', '🥢', '4.8', '41', '40.000–60.000đ', 'Miền Trung', '25 phút', 'Sợi mì dai, thịt xá xíu, rau sống và chút nước sốt đậm vị.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('pho-cuon', 'noodles', 'Phở cuốn', '🌯', '4.6', '27', '35.000–50.000đ', 'Miền Bắc', '20 phút', 'Bánh phở cuốn thịt bò xào, ăn cùng rau thơm và nước chấm.', 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=85', '1'),
    ('bun-thit-nuong', 'noodles', 'Bún thịt nướng', '🍢', '4.7', '56', '35.000–50.000đ', 'Miền Nam', '25 phút', 'Thịt nướng thơm, bún tươi, rau và nước mắm pha vừa miệng.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?auto=format&fit=crop&w=1200&q=85', '1'),
    ('chao-suon', 'soup', 'Cháo sườn', '🥣', '4.5', '24', '25.000–40.000đ', 'Miền Bắc', '25 phút', 'Cháo mịn nóng hổi, sườn mềm, thêm quẩy giòn cho ngày se lạnh.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('banh-canh-cua', 'soup', 'Bánh canh cua', '🦀', '4.8', '47', '45.000–65.000đ', 'Miền Nam', '30 phút', 'Sợi bánh canh dai mềm trong nước dùng sánh thơm vị cua.', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=85', '1'),
    ('goi-ngo-sen', 'vegetarian', 'Gỏi ngó sen', '🥗', '4.5', '18', '30.000–45.000đ', 'Miền Nam', '20 phút', 'Món chay thanh mát với ngó sen giòn, rau thơm và nước trộn chua ngọt.', 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=85', '1'),
    ('com-chay-nam', 'vegetarian', 'Cơm chay nấm', '🍄', '4.6', '22', '30.000–45.000đ', 'Món chay', '25 phút', 'Cơm nóng ăn cùng nấm xào và rau củ theo mùa.', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=85', '1'),
    ('xoi-xeo', 'rice', 'Xôi xéo', '🍚', '4.5', '29', '20.000–35.000đ', 'Miền Bắc', '15 phút', 'Xôi nếp dẻo thơm, đậu xanh tơi, hành phi vàng ruộm.', 'https://images.unsplash.com/photo-1516684732162-798a0062be99?auto=format&fit=crop&w=1200&q=85', '1'),
    ('banh-trang-tron', 'snack', 'Bánh tráng trộn', '🥗', '4.5', '64', '20.000–35.000đ', 'Miền Nam', '10 phút', 'Bánh tráng dai mềm trộn cùng xoài xanh, trứng cút và rau răm.', 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=85', '1');

INSERT IGNORE INTO users (id, name, email, role, status) VALUES
    ('user-1', 'Nguyễn Minh Anh', 'minhanh@example.com', 'user', 'Hoạt động'),
    ('user-2', 'Trần Hoàng Nam', 'hoangnam@example.com', 'user', 'Hoạt động'),
    ('user-3', 'Lê Thu Hà', 'thuha@example.com', 'user', 'Chờ xác nhận'),
    ('user-4', 'Phạm Gia Bảo', 'giabao@example.com', 'admin', 'Hoạt động');

INSERT IGNORE INTO reviews (id, meal_id, reviewer_name, rating, review_text, status) VALUES
    ('review-1', 'pho-bo-ha-noi', 'Minh Anh', '5', 'Nước dùng thơm, rất vừa miệng.', 'approved'),
    ('review-2', 'bun-bo-hue', 'Hoàng Nam', '4', 'Đậm vị, hơi cay nhưng ngon.', 'pending'),
    ('review-3', 'com-tam-suon', 'Thu Hà', '5', 'Sườn mềm, phần ăn vừa đủ.', 'pending');

COMMIT;

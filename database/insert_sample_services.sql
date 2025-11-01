-- Sample Services với ảnh placeholder
-- Sau khi chạy script này, anh thay ảnh placeholder bằng ảnh thật

-- Cắt tóc
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1622286346003-c2f5f2f8b43e?w=500',
    description = 'Dịch vụ cắt tóc chuyên nghiệp, tạo kiểu theo xu hướng. Bao gồm gội đầu, cắt và tạo kiểu cơ bản.'
WHERE service_id = 1;

-- Nhuộm tóc
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=500',
    description = 'Nhuộm tóc với sản phẩm cao cấp, đa dạng màu sắc. Bao gồm tư vấn màu, nhuộm và dưỡng tóc.'
WHERE service_id = 2;

-- Uốn tóc
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=500',
    description = 'Uốn tóc xoăn, uốn sóng với công nghệ hiện đại, không gây hại cho tóc. Hiệu quả lâu dài.'
WHERE service_id = 3;

-- Duỗi tóc
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1562322140-8baeececf3df?w=500',
    description = 'Duỗi thẳng tóc tự nhiên, mượt mà. Công nghệ Keratin giúp tóc suôn thẳng bóng khỏe.'
WHERE service_id = 4;

-- Nối tóc
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=500',
    description = 'Nối tóc tự nhiên, không nhìn ra. Nhiều phương pháp: kẹp, hàn, dán phù hợp từng người.'
WHERE service_id = 5;

-- Gội đầu massage
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=500',
    description = 'Gội đầu dưỡng sinh kết hợp massage thư giãn. Sử dụng sản phẩm cao cấp chăm sóc da đầu.'
WHERE service_id = 6;

-- Làm móng tay
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=500',
    description = 'Manicure chuyên nghiệp, nhiều mẫu mã đa dạng. Sơn gel bền màu, nail art sáng tạo.'
WHERE service_id = 7;

-- Làm móng chân
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1519415510236-718bdfcd89c8?w=500',
    description = 'Pedicure thư giãn, chăm sóc đôi chân. Massage, tẩy tế bào chết, sơn móng đẹp.'
WHERE service_id = 8;

-- Chăm sóc da mặt
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=500',
    description = 'Chăm sóc da mặt chuyên sâu với quy trình chuẩn spa. Làm sạch, massage, đắp mặt nạ.'
WHERE service_id = 9;

-- Tắm trắng
UPDATE services SET 
    image = 'https://images.unsplash.com/photo-1560750588-73207b1ef5b8?w=500',
    description = 'Dịch vụ tắm trắng toàn thân với công nghệ hiện đại. Da trắng sáng, mịn màng tự nhiên.'
WHERE service_id = 10;

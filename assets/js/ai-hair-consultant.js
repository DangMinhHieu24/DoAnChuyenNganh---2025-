/**
 * AI Hair Consultant JavaScript
 * Xử lý upload ảnh và hiển thị kết quả
 */

let selectedFile = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeUpload();
});

/**
 * Khởi tạo upload functionality
 */
function initializeUpload() {
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const imageInput = document.getElementById('imageInput');
    
    // Click to upload
    imageInput.addEventListener('change', handleFileSelect);
    
    // Drag and drop
    uploadPlaceholder.addEventListener('dragover', handleDragOver);
    uploadPlaceholder.addEventListener('dragleave', handleDragLeave);
    uploadPlaceholder.addEventListener('drop', handleDrop);
}

/**
 * Xử lý khi chọn file
 */
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        validateAndPreviewImage(file);
    }
}

/**
 * Xử lý drag over
 */
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.add('drag-over');
}

/**
 * Xử lý drag leave
 */
function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('drag-over');
}

/**
 * Xử lý drop file
 */
function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        validateAndPreviewImage(files[0]);
    }
}

/**
 * Validate và preview ảnh
 */
function validateAndPreviewImage(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Chỉ chấp nhận file JPG, PNG, WEBP', 'error');
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('Ảnh quá lớn. Tối đa 5MB', 'error');
        return;
    }
    
    selectedFile = file;
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('previewImage').src = e.target.result;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('imagePreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

/**
 * Reset upload
 */
function resetUpload() {
    selectedFile = null;
    document.getElementById('imageInput').value = '';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('analyzingState').style.display = 'none';
    document.getElementById('resultSection').style.display = 'none';
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Phân tích ảnh
 */
async function analyzeImage() {
    if (!selectedFile) {
        showAlert('Vui lòng chọn ảnh', 'error');
        return;
    }
    
    // Show analyzing state
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('analyzingState').style.display = 'block';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('action', 'analyze_face');
    formData.append('image', selectedFile);
    
    try {
        const response = await fetch('/Website_DatLich/api/ai-hair-consultant.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayResult(data);
        } else {
            showAlert(data.message || 'Có lỗi xảy ra', 'error');
            document.getElementById('analyzingState').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'block';
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Không thể kết nối đến server', 'error');
        document.getElementById('analyzingState').style.display = 'none';
        document.getElementById('imagePreview').style.display = 'block';
    }
}

/**
 * Hiển thị kết quả
 */
function displayResult(data) {
    // Hide analyzing state
    document.getElementById('analyzingState').style.display = 'none';
    
    // Set result image
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('resultImage').src = e.target.result;
    };
    reader.readAsDataURL(selectedFile);
    
    // Format and display analysis
    const analysisHtml = formatAnalysis(data.analysis);
    document.getElementById('analysisResult').innerHTML = analysisHtml;
    
    // Show result section
    document.getElementById('resultSection').style.display = 'block';
    
    // Scroll to result
    setTimeout(() => {
        document.getElementById('resultSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }, 300);
    
    // Show success message
    showAlert('Phân tích thành công! 🎨', 'success');
}

/**
 * Format analysis text
 */
function formatAnalysis(text) {
    // Convert markdown-like formatting to HTML
    let html = text;
    
    // Bold text
    html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    
    // Line breaks
    html = html.replace(/\n/g, '<br>');
    
    // Headers (##)
    html = html.replace(/##\s*(.+?)<br>/g, '<h4>$1</h4>');
    
    // Lists
    html = html.replace(/- (.+?)<br>/g, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
    
    // Numbers (1., 2., 3.)
    html = html.replace(/(\d+)\.\s*<strong>(.+?)<\/strong>/g, '<div class="hairstyle-item"><span class="number">$1</span><strong>$2</strong></div>');
    
    return html;
}

/**
 * Scroll to booking
 */
function scrollToBooking() {
    // Redirect to booking page
    window.location.href = '/Website_DatLich/pages/booking.php';
}

/**
 * Show alert
 */
function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Add custom styles for hairstyle items
const style = document.createElement('style');
style.textContent = `
    .hairstyle-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-left: 4px solid #667eea;
        padding: 20px;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .hairstyle-item .number {
        display: inline-block;
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        font-weight: 700;
        margin-right: 10px;
    }
    
    .analysis-content ul {
        background: #f8f9fa;
        padding: 20px 20px 20px 40px;
        border-radius: 8px;
        margin: 15px 0;
    }
    
    .analysis-content li {
        color: #475569;
        line-height: 1.8;
    }
`;
document.head.appendChild(style);

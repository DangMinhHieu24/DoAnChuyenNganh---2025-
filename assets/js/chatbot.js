/**
 * Chatbot Widget JavaScript
 * Xử lý giao diện và logic chat với AI
 */

class SalonChatbot {
    constructor() {
        this.isOpen = false;
        this.messages = [];
        // Lấy base URL từ window hoặc dùng relative path
        const baseUrl = window.location.origin + '/Website_DatLich';
        this.apiUrl = baseUrl + '/api/chatbot.php';
        this.bookingApiUrl = baseUrl + '/api/chatbot-booking.php';
        this.isBookingMode = false;
        this.bookingState = null;
        
        this.init();
    }
    
    init() {
        this.createWidget();
        this.attachEventListeners();
        this.showWelcomeMessage();
    }
    
    createWidget() {
        const widget = document.createElement('div');
        widget.innerHTML = `
            <!-- Toggle Button -->
            <button class="chatbot-toggle" id="chatbotToggle">
                <i class="fas fa-comments"></i>
            </button>
            
            <!-- Chatbot Container -->
            <div class="chatbot-container" id="chatbotContainer">
                <!-- Header -->
                <div class="chatbot-header">
                    <div class="chatbot-header-info">
                        <div class="chatbot-avatar">
                            🤖
                        </div>
                        <div class="chatbot-title">
                            <h3>Trợ lý AI Salon</h3>
                            <div class="chatbot-status">
                                <span class="status-dot"></span>
                                <span>Đang hoạt động</span>
                            </div>
                        </div>
                    </div>
                    <button class="chatbot-close" id="chatbotClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Messages Area -->
                <div class="chatbot-messages" id="chatbotMessages">
                    <!-- Messages will be added here -->
                </div>
                
                <!-- Input Area -->
                <div class="chatbot-input">
                    <input 
                        type="text" 
                        id="chatbotInput" 
                        placeholder="Nhập tin nhắn của bạn..."
                        autocomplete="off"
                    />
                    <button class="chatbot-send-btn" id="chatbotSend">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(widget);
    }
    
    attachEventListeners() {
        const toggle = document.getElementById('chatbotToggle');
        const close = document.getElementById('chatbotClose');
        const send = document.getElementById('chatbotSend');
        const input = document.getElementById('chatbotInput');
        
        toggle.addEventListener('click', () => this.toggleChat());
        close.addEventListener('click', () => this.toggleChat());
        send.addEventListener('click', () => this.sendMessage());
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        const container = document.getElementById('chatbotContainer');
        const toggle = document.getElementById('chatbotToggle');
        
        if (this.isOpen) {
            container.classList.add('active');
            toggle.classList.add('active');
            document.getElementById('chatbotInput').focus();
        } else {
            container.classList.remove('active');
            toggle.classList.remove('active');
        }
    }
    
    showWelcomeMessage() {
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.innerHTML = `
            <div class="welcome-message">
                <div class="icon">👋</div>
                <h4>Xin chào! Tôi là trợ lý AI</h4>
                <p>Tôi có thể giúp bạn:</p>
                <div class="quick-replies" style="margin-top: 16px; justify-content: center;">
                    <button class="quick-reply-btn" onclick="chatbot.sendQuickReply('Xem danh sách dịch vụ')">
                        📋 Xem dịch vụ
                    </button>
                    <button class="quick-reply-btn" onclick="chatbot.sendQuickReply('Giá dịch vụ cắt tóc')">
                        💰 Xem giá
                    </button>
                    <button class="quick-reply-btn" onclick="chatbot.sendQuickReply('Nhân viên nào giỏi?')">
                        👨‍💼 Xem nhân viên
                    </button>
                    <button class="quick-reply-btn" onclick="chatbot.sendQuickReply('Đặt lịch hẹn')">
                        📅 Đặt lịch
                    </button>
                </div>
            </div>
        `;
    }
    
    sendQuickReply(message) {
        document.getElementById('chatbotInput').value = message;
        this.sendMessage();
    }
    
    async sendMessage() {
        const input = document.getElementById('chatbotInput');
        const message = input.value.trim();
        
        if (!message) return;
        
        // Clear input
        input.value = '';
        
        // Add user message to UI
        this.addMessage('user', message);
        
        // Show typing indicator
        this.showTypingIndicator();
        
        try {
            // Call API
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            // Hide typing indicator
            this.hideTypingIndicator();
            
            if (data.success) {
                // Add bot response
                this.addMessage('bot', data.message);
                
                // Add quick replies based on intent
                this.addQuickReplies(data.intent);
            } else {
                this.addMessage('bot', data.message || 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.');
            }
        } catch (error) {
            console.error('Error:', error);
            this.hideTypingIndicator();
            this.addMessage('bot', 'Xin lỗi, không thể kết nối đến server. Vui lòng thử lại sau.');
        }
    }
    
    addMessage(sender, text) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const time = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const avatar = sender === 'user' ? '👤' : '🤖';
        
        messageDiv.innerHTML = `
            <div class="message-avatar">${avatar}</div>
            <div class="message-content">
                <div class="message-bubble">${this.formatMessage(text)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
        
        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        
        // Store message
        this.messages.push({ sender, text, time });
    }
    
    formatMessage(text) {
        // Convert line breaks
        text = text.replace(/\n/g, '<br>');
        
        // Convert bold text
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Convert links
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        
        return text;
    }
    
    showTypingIndicator() {
        const messagesContainer = document.getElementById('chatbotMessages');
        
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">🤖</div>
            <div class="message-content">
                <div class="typing-indicator active">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    addQuickReplies(intent) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const lastMessage = messagesContainer.lastElementChild;
        
        let replies = [];
        
        switch (intent) {
            case 'list_services':
                replies = [
                    'Giá dịch vụ cắt tóc',
                    'Giá dịch vụ nhuộm',
                    '📅 Đặt lịch ngay'
                ];
                break;
            case 'price_inquiry':
                replies = [
                    'Xem tất cả dịch vụ',
                    'Nhân viên nào giỏi?',
                    '📅 Đặt lịch hẹn'
                ];
                break;
            case 'staff_inquiry':
                replies = [
                    'Kiểm tra lịch trống',
                    'Xem dịch vụ',
                    '📅 Đặt lịch ngay'
                ];
                break;
            case 'booking':
                // Thêm nút đặt lịch trực tiếp
                replies = [
                    '🔗 Mở trang đặt lịch',
                    'Xem dịch vụ',
                    'Gọi điện: 0976985305'
                ];
                break;
            case 'working_hours':
                replies = [
                    'Địa chỉ salon',
                    'Số điện thoại',
                    '📅 Đặt lịch hẹn'
                ];
                break;
            case 'contact_info':
                replies = [
                    'Giờ làm việc',
                    'Xem dịch vụ',
                    '📅 Đặt lịch'
                ];
                break;
            default:
                replies = [
                    'Xem dịch vụ',
                    'Xem giá',
                    '📅 Đặt lịch'
                ];
        }
        
        if (replies.length > 0) {
            const quickRepliesDiv = document.createElement('div');
            quickRepliesDiv.className = 'quick-replies';
            quickRepliesDiv.style.marginTop = '8px';
            
            replies.forEach(reply => {
                const btn = document.createElement('button');
                btn.className = 'quick-reply-btn';
                btn.textContent = reply;
                
                // Xử lý đặc biệt cho nút đặt lịch
                if (reply.includes('Mở trang đặt lịch')) {
                    btn.onclick = () => {
                        window.location.href = window.location.origin + '/Website_DatLich/pages/booking.php';
                    };
                } else if (reply.includes('Gọi điện')) {
                    btn.onclick = () => {
                        window.location.href = 'tel:0976985305';
                    };
                } else if (reply.includes('📅 Đặt lịch')) {
                    btn.onclick = () => this.startBooking();
                } else {
                    btn.onclick = () => this.sendQuickReply(reply);
                }
                
                quickRepliesDiv.appendChild(btn);
            });
            
            const messageContent = lastMessage.querySelector('.message-content');
            messageContent.appendChild(quickRepliesDiv);
        }
    }
    
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbotMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    clearChat() {
        this.messages = [];
        this.showWelcomeMessage();
    }
    
    /**
     * Bắt đầu đặt lịch
     */
    async startBooking() {
        this.isBookingMode = true;
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'start_booking' })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.require_login) {
                this.addMessage('bot', data.message);
                this.addLoginButton();
                return;
            }
            
            if (data.success) {
                this.bookingState = data;
                this.addMessage('bot', data.message);
                this.showServiceSelection(data.categories);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại! 😔');
        }
    }
    
    /**
     * Hiển thị lựa chọn dịch vụ
     */
    showServiceSelection(categories) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const selectionDiv = document.createElement('div');
        selectionDiv.className = 'booking-selection';
        
        let html = '<div class="service-categories">';
        
        for (const [catName, services] of Object.entries(categories)) {
            html += `<div class="category-group">`;
            html += `<h5>${catName}</h5>`;
            
            services.forEach(service => {
                html += `
                    <button class="service-option" onclick="chatbot.selectService(${service.service_id})">
                        <div class="service-name">${service.service_name}</div>
                        <div class="service-info">
                            <span class="price">${new Intl.NumberFormat('vi-VN').format(service.price)}đ</span>
                            <span class="duration">${service.duration} phút</span>
                        </div>
                    </button>
                `;
            });
            
            html += `</div>`;
        }
        
        html += '</div>';
        selectionDiv.innerHTML = html;
        messagesContainer.appendChild(selectionDiv);
        this.scrollToBottom();
    }
    
    /**
     * Chọn dịch vụ
     */
    async selectService(serviceId) {
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'select_service', service_id: serviceId })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage('bot', data.message);
                this.showStaffSelection(data.staff_list);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Hiển thị lựa chọn nhân viên
     */
    showStaffSelection(staffList) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const selectionDiv = document.createElement('div');
        selectionDiv.className = 'booking-selection';
        
        let html = '<div class="staff-list">';
        
        staffList.forEach(staff => {
            html += `
                <button class="staff-option" onclick="chatbot.selectStaff(${staff.staff_id})">
                    <div class="staff-name">${staff.full_name}</div>
                    <div class="staff-info">
                        ${staff.specialization ? `<span>${staff.specialization}</span>` : ''}
                        ${staff.rating ? `<span>⭐ ${staff.rating}/5.0</span>` : ''}
                    </div>
                </button>
            `;
        });
        
        html += '</div>';
        selectionDiv.innerHTML = html;
        messagesContainer.appendChild(selectionDiv);
        this.scrollToBottom();
    }
    
    /**
     * Chọn nhân viên
     */
    async selectStaff(staffId) {
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'select_staff', staff_id: staffId })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage('bot', data.message);
                this.showDateSelection(data.dates);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Hiển thị lựa chọn ngày
     */
    showDateSelection(dates) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const selectionDiv = document.createElement('div');
        selectionDiv.className = 'booking-selection';
        
        let html = '<div class="date-list">';
        
        dates.forEach(dateObj => {
            html += `
                <button class="date-option" onclick="chatbot.selectDate('${dateObj.date}')">
                    ${dateObj.display}
                </button>
            `;
        });
        
        html += '</div>';
        selectionDiv.innerHTML = html;
        messagesContainer.appendChild(selectionDiv);
        this.scrollToBottom();
    }
    
    /**
     * Chọn ngày
     */
    async selectDate(date) {
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'select_date', date: date })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage('bot', data.message);
                this.showTimeSelection(data.available_slots);
            } else {
                this.addMessage('bot', data.message);
                if (data.step === 'select_date') {
                    // Quay lại chọn ngày
                    setTimeout(() => this.selectStaff(this.bookingState.staff_id), 1000);
                }
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Hiển thị lựa chọn giờ
     */
    showTimeSelection(slots) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const selectionDiv = document.createElement('div');
        selectionDiv.className = 'booking-selection';
        
        let html = '<div class="time-slots">';
        
        slots.forEach(time => {
            html += `
                <button class="time-option" onclick="chatbot.selectTime('${time}')">
                    ${time}
                </button>
            `;
        });
        
        html += '</div>';
        selectionDiv.innerHTML = html;
        messagesContainer.appendChild(selectionDiv);
        this.scrollToBottom();
    }
    
    /**
     * Chọn giờ
     */
    async selectTime(time) {
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'select_time', time: time })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            if (data.success) {
                this.addMessage('bot', data.message);
                this.showBookingSummary(data.summary);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Hiển thị tóm tắt và xác nhận
     */
    showBookingSummary(summary) {
        const messagesContainer = document.getElementById('chatbotMessages');
        const summaryDiv = document.createElement('div');
        summaryDiv.className = 'booking-summary';
        
        let html = `
            <div class="summary-card">
                <div class="summary-item"><strong>Dịch vụ:</strong> ${summary.service}</div>
                <div class="summary-item"><strong>Nhân viên:</strong> ${summary.staff}</div>
                <div class="summary-item"><strong>Ngày:</strong> ${summary.date}</div>
                <div class="summary-item"><strong>Giờ:</strong> ${summary.time}</div>
                <div class="summary-item"><strong>Thời gian:</strong> ${summary.duration}</div>
                <div class="summary-item price-item"><strong>Giá:</strong> ${summary.price}</div>
            </div>
            <div class="confirm-buttons">
                <button class="btn-confirm" onclick="chatbot.confirmBooking()">✅ Xác nhận đặt lịch</button>
                <button class="btn-cancel" onclick="chatbot.cancelBooking()">❌ Hủy</button>
            </div>
        `;
        
        summaryDiv.innerHTML = html;
        messagesContainer.appendChild(summaryDiv);
        this.scrollToBottom();
    }
    
    /**
     * Xác nhận đặt lịch
     */
    async confirmBooking() {
        this.showTypingIndicator();
        
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'confirm_booking' })
            });
            
            const data = await response.json();
            this.hideTypingIndicator();
            
            this.addMessage('bot', data.message);
            this.isBookingMode = false;
            
            if (data.success) {
                // Thêm quick replies
                this.addQuickReplies('booking_success');
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('bot', 'Có lỗi xảy ra. Vui lòng thử lại!');
        }
    }
    
    /**
     * Hủy đặt lịch
     */
    async cancelBooking() {
        try {
            const response = await fetch(this.bookingApiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'cancel_booking' })
            });
            
            const data = await response.json();
            this.addMessage('bot', data.message);
            this.isBookingMode = false;
        } catch (error) {
            this.addMessage('bot', 'Đã hủy đặt lịch.');
            this.isBookingMode = false;
        }
    }
    
    /**
     * Thêm nút đăng nhập
     */
    addLoginButton() {
        const messagesContainer = document.getElementById('chatbotMessages');
        const btnDiv = document.createElement('div');
        btnDiv.className = 'booking-selection';
        btnDiv.innerHTML = `
            <button class="btn-login" onclick="window.location.href='/Website_DatLich/auth/login.php'">
                🔐 Đăng nhập ngay
            </button>
        `;
        messagesContainer.appendChild(btnDiv);
        this.scrollToBottom();
    }
}

// Initialize chatbot when DOM is ready
let chatbot;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        chatbot = new SalonChatbot();
    });
} else {
    chatbot = new SalonChatbot();
}

document.addEventListener('DOMContentLoaded', function() {
    // Default chat responses
    const chatResponses = [
      {
        questionKey: "booking",
        question: "How do I make a booking?",
        answer: "Sign up an account and you can make a booking through our website by clicking on 'Book Now' button. Select your check-in and check-out dates, choose a room, and follow the instructions to complete your reservation."
      },
      {
        questionKey: "payment",
        question: "What payment methods do you accept?",
        answer: "We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and bank transfers. Payment is securely processed during the booking process."
      },
      {
        questionKey: "cancellation",
        question: "What is your cancellation policy?",
        answer: "Our standard cancellation policy allows free cancellation up to 48 hours before check-in. Cancellations made within 48 hours of check-in may be subject to a fee equal to one night's stay."
      },
      {
        questionKey: "checkin",
        question: "What are the check-in and check-out times?",
        answer: "Check-in is from 2:00 PM onwards, and check-out is until 12:00 PM (noon) strict. Early check-in or late check-out may be available upon request, subject to availability."
      },
      {
        questionKey: "amenities",
        question: "What amenities are included?",
        answer: "Our staycation includes: PS4 with games, Billiards Table, Portable Karaoke, Board Games & Card Games, Smart TV with Netflix, and access to Azure's wave pool and man-made beach."
      }
    ];
  
    // Chat configuration
    const chatConfig = {
      initialMessage: "Hi there! 👋 How can I help you with your staycation today?",
      placeholderText: "Type your message...",
      supportTeamName: "El Sanchel Support",
      supportTeamStatus: "Typically replies in a few minutes",
      typingDelay: 1000 // milliseconds
    };
  
    // Chat widget DOM elements
    const chatWidgetContainer = document.createElement('div');
    chatWidgetContainer.className = 'chat-widget-container';
    
    // Create chat button
    const chatButton = document.createElement('button');
    chatButton.className = 'chat-button';
    chatButton.setAttribute('aria-label', 'Open chat');
    chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>';
    
    // Create chat window
    const chatWindow = document.createElement('div');
    chatWindow.className = 'chat-window';
    
    // Chat header
    const chatHeader = document.createElement('div');
    chatHeader.className = 'chat-header';
    
    const chatTitle = document.createElement('div');
    chatTitle.className = 'chat-title';
    
    const chatTitleAvatar = document.createElement('div');
    chatTitleAvatar.className = 'chat-title-avatar';
    chatTitleAvatar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
    
    const chatTitleInfo = document.createElement('div');
    chatTitleInfo.className = 'chat-title-info';
    chatTitleInfo.innerHTML = `<h3>${chatConfig.supportTeamName}</h3><p>${chatConfig.supportTeamStatus}</p>`;
    
    const chatCloseBtn = document.createElement('button');
    chatCloseBtn.className = 'chat-close-btn';
    chatCloseBtn.setAttribute('aria-label', 'Close chat');
    chatCloseBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
    
    // Chat messages container
    const chatMessages = document.createElement('div');
    chatMessages.className = 'chat-messages';
    
    // Chat input
    const chatInputContainer = document.createElement('div');
    chatInputContainer.className = 'chat-input';
    
    const chatInputField = document.createElement('div');
    chatInputField.className = 'chat-input-field';
    
    const chatInputElement = document.createElement('input');
    chatInputElement.type = 'text';
    chatInputElement.placeholder = chatConfig.placeholderText;
    
    const chatSendBtn = document.createElement('button');
    chatSendBtn.className = 'chat-send-btn';
    chatSendBtn.disabled = true;
    chatSendBtn.setAttribute('aria-label', 'Send message');
    chatSendBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
    
    // Assemble the chat widget
    chatTitle.appendChild(chatTitleAvatar);
    chatTitle.appendChild(chatTitleInfo);
    
    chatHeader.appendChild(chatTitle);
    chatHeader.appendChild(chatCloseBtn);
    
    chatInputField.appendChild(chatInputElement);
    chatInputContainer.appendChild(chatInputField);
    chatInputContainer.appendChild(chatSendBtn);
    
    chatWindow.appendChild(chatHeader);
    chatWindow.appendChild(chatMessages);
    chatWindow.appendChild(chatInputContainer);
    
    chatWidgetContainer.appendChild(chatWindow);
    chatWidgetContainer.appendChild(chatButton);
    
    // Insert the chat widget before the back-to-top button
    const backToTopBtn = document.querySelector('.back-to-top');
    if (backToTopBtn) {
      backToTopBtn.parentNode.insertBefore(chatWidgetContainer, backToTopBtn);
    } else {
      document.body.appendChild(chatWidgetContainer);
    }
    
    // Chat widget state and functionality
    let isOpen = false;
    let isTyping = false;
    let messages = [];
    
    // Open/close chat window
    function toggleChat() {
      isOpen = !isOpen;
      if (isOpen) {
        chatWindow.classList.add('open');
        chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
        chatButton.setAttribute('aria-label', 'Close chat');
        
        // If no messages yet, add initial messages
        if (messages.length === 0) {
          addMessage(chatConfig.initialMessage, false);
          setTimeout(() => {
            addMessage("Here are some common questions I can help with:", false);
            showQuickReplies();
          }, 500);
        }
        
        // Focus the input field
        setTimeout(() => {
          chatInputElement.focus();
        }, 300);
      } else {
        chatWindow.classList.remove('open');
        chatButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="1"></circle><circle cx="15" cy="10" r="1"></circle></svg>';
        chatButton.setAttribute('aria-label', 'Open chat');
      }
    }
    
    // Event listeners
    chatButton.addEventListener('click', toggleChat);
    chatCloseBtn.addEventListener('click', () => {
      if (isOpen) toggleChat();
    });
    
    // Handle sending messages
    function handleSendMessage() {
      const message = chatInputElement.value.trim();
      if (message && !isTyping) {
        addMessage(message, true);
        chatInputElement.value = '';
        chatSendBtn.disabled = true;
        
        // Find matching response or use fallback
        processUserMessage(message);
      }
    }
    
    chatSendBtn.addEventListener('click', handleSendMessage);
    chatInputElement.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        handleSendMessage();
      }
    });
    
    chatInputElement.addEventListener('input', () => {
      chatSendBtn.disabled = chatInputElement.value.trim() === '';
    });
    
    // Add a message to the chat
    function addMessage(text, isUser) {
      const messageEl = document.createElement('div');
      messageEl.className = isUser ? 'chat-message user' : 'chat-message bot';
      
      let messageContent = '';
      
      if (!isUser) {
        messageContent += `
          <div class="chat-message-avatar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
        `;
      }
      
      messageContent += `<div class="chat-message-bubble">${text}</div>`;
      messageEl.innerHTML = messageContent;
      
      chatMessages.appendChild(messageEl);
      
      // Store the message
      messages.push({ text, isUser });
      
      // Scroll to bottom
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Show typing indicator
    function showTypingIndicator() {
      isTyping = true;
      
      const typingEl = document.createElement('div');
      typingEl.className = 'chat-message bot typing-message';
      typingEl.innerHTML = `
        <div class="chat-message-avatar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
        <div class="chat-message-bubble">
          <div class="typing-indicator">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
          </div>
        </div>
      `;
      
      chatMessages.appendChild(typingEl);
      chatMessages.scrollTop = chatMessages.scrollHeight;
      
      return typingEl;
    }
    
    // Hide typing indicator
    function hideTypingIndicator(typingEl) {
      chatMessages.removeChild(typingEl);
      isTyping = false;
    }
    
    // Process user message and provide a response
    function processUserMessage(message) {
      // Show typing indicator
      const typingEl = showTypingIndicator();
      
      // Try to find a matching response
      const matchResponse = chatResponses.find(r => 
        message.toLowerCase().includes(r.question.toLowerCase()) ||
        r.questionKey.toLowerCase().includes(message.toLowerCase())
      );
      
      // Calculate typing delay based on response length
      const response = matchResponse ? matchResponse.answer : 
        "Thank you for your message. Our team will get back to you shortly. In the meantime, feel free to check out our suggested questions.";
      
      const typingDelay = Math.min(
        chatConfig.typingDelay + response.length * 20, 
        3000  // Cap at 3 seconds
      );
      
      // Simulate bot typing and then respond
      setTimeout(() => {
        hideTypingIndicator(typingEl);
        addMessage(response, false);
        
        // Show quick replies again after providing a response
        setTimeout(() => {
          showQuickReplies();
        }, 800);
      }, typingDelay);
    }
    
    // Show quick reply buttons
    function showQuickReplies() {
      // Remove previous quick replies if they exist
      const previousQuickReplies = document.querySelector('.quick-replies');
      if (previousQuickReplies) {
        chatMessages.removeChild(previousQuickReplies);
      }
      
      const quickRepliesContainer = document.createElement('div');
      quickRepliesContainer.className = 'quick-replies';
      
      chatResponses.forEach(response => {
        const quickReplyBtn = document.createElement('button');
        quickReplyBtn.className = 'quick-reply-btn';
        quickReplyBtn.textContent = response.question;
        
        quickReplyBtn.addEventListener('click', () => {
          if (isTyping) return; // Prevent clicking during typing animation
          
          addMessage(response.question, true);
          
          // Show typing indicator
          const typingEl = showTypingIndicator();
          
          // Calculate typing delay based on response length
          const typingDelay = Math.min(
            chatConfig.typingDelay + response.answer.length * 20, 
            3000  // Cap at 3 seconds
          );
          
          // Hide quick replies when a reply is selected
          chatMessages.removeChild(quickRepliesContainer);
          
          // Simulate bot typing and then respond
          setTimeout(() => {
            hideTypingIndicator(typingEl);
            addMessage(response.answer, false);
            
            // Show quick replies again after bot responds
            setTimeout(() => {
              showQuickReplies();
            }, 800);
          }, typingDelay);
        });
        
        quickRepliesContainer.appendChild(quickReplyBtn);
      });
      
      chatMessages.appendChild(quickRepliesContainer);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Close chat when Escape key is pressed
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isOpen) {
        toggleChat();
      }
    });
  });
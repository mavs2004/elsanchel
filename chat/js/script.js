$(document).ready(function() {
    const chatForm = $('#chat-form');
    const messageInput = $('#message-input');
    const chatMessages = $('#chat-messages');
    const emojiBtn = $('#emoji-btn');
    const emojiPicker = $('#emoji-picker');
    const emojiContainer = $('#emoji-container');
    const fileInput = $('#file-input');
    const refreshBtn = $('#refresh-chat');
    const searchUser = $('#search-user');
    let incoming_id = $('input[name="incoming_id"]').val();
    let scrollBottom = true;
    let emojiPickerVisible = false;

    // Load emojis
    const emojis = {
        smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚'],
        animals: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🐔', '🐧', '🐦', '🐤', '🦄'],
        food: ['🍏', '🍎', '🍐', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦'],
        travel: ['🚗', '🚕', '🚙', '🚌', '🚎', '🏎', '🚓', '🚑', '🚒', '🚐', '🚚', '🚛', '🚜', '🛴', '🚲', '🛵', '🏍', '🚨', '🚔', '✈️']
    };

    // Populate emoji picker
    function populateEmojis(category) {
        emojiContainer.empty();
        emojis[category].forEach(emoji => {
            emojiContainer.append(`<span class="emoji">${emoji}</span>`);
        });
    }

    // Show first category by default
    populateEmojis('smileys');

    // Toggle emoji picker
    emojiBtn.click(function() {
        emojiPickerVisible = !emojiPickerVisible;
        if(emojiPickerVisible) {
            emojiPicker.fadeIn();
        } else {
            emojiPicker.fadeOut();
        }
    });

    // Handle emoji category selection
    $('.emoji-categories button').click(function() {
        const category = $(this).data('category');
        populateEmojis(category);
    });

    // Insert emoji into message
    emojiContainer.on('click', '.emoji', function() {
        messageInput.val(messageInput.val() + $(this).text());
        messageInput.focus();
    });

    // Handle file upload
    fileInput.change(function() {
        if(this.files.length > 0) {
            const file = this.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'upload_file');

            $.ajax({
                url: 'php/ajax.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.status === 'success') {
                        const fileType = file.type.split('/')[0];
                        let message = '';

                        if(fileType === 'image') {
                            message = `<img src="${response.file_path}" alt="${response.file_name}" class="chat-image">`;
                        } else {
                            message = `<a href="${response.file_path}" target="_blank">${response.file_name}</a>`;
                        }

                        // Send the file as a message
                        $('input[name="message"]').val(message);
                        chatForm.submit();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });

    // Handle form submission
    chatForm.on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const message = messageInput.val().trim();
        
        if(message !== '') {
            $.post('php/ajax.php', formData + '&action=send_message', function(data) {
                if(data.status === 'success') {
                    messageInput.val('');
                    scrollBottom = true;
                    getMessages();
                }
            }, 'json');
        }
    });

    // Get messages from database
    function getMessages() {
        $.post('php/ajax.php', {
            action: 'get_messages',
            incoming_id: incoming_id
        }, function(data) {
            if(data.status === 'success') {
                chatMessages.html(data.html);
                if(scrollBottom) {
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                }
            }
        }, 'json');
    }

    // Get users list (for admin)
    function getUsers(search = '') {
        $.post('php/ajax.php', {
            action: 'get_users',
            search: search
        }, function(data) {
            if(data.status === 'success') {
                $('#users-container').html(data.html);
                
                // Handle user selection
                $('.user').click(function() {
                    $('.user').removeClass('active');
                    $(this).addClass('active');
                    incoming_id = $(this).data('userid');
                    $('input[name="incoming_id"]').val(incoming_id);
                    scrollBottom = true;
                    getMessages();
                });
            }
        }, 'json');
    }

    // Check if user is admin and load users list
    if($('.users-list').length) {
        getUsers();
        
        // Search users
        searchUser.on('input', function() {
            getUsers($(this).val());
        });
    }

    // Refresh messages
    refreshBtn.click(function() {
        scrollBottom = true;
        getMessages();
    });

    // Auto-focus input field
    messageInput.focus();

    // Check for new messages every 3 seconds
    setInterval(getMessages, 3000);

    // Initial load
    getMessages();
});
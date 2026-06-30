<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Inteligente - Ministerio Público Distrito Fiscal de Arequipa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <!-- Header del Chat -->
        <div class="chat-header">
            <div class="header-content">
                <div class="bot-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                </div>
                <div class="header-info">
                    <h1>MP BOT</h1>
                    <p class="status"><span class="status-dot"></span> En línea</p>
                </div>
            </div>
        </div>

        <!-- Área de mensajes -->
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <div class="message-content">
                    <p>¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?</p>
                </div>
                <div class="message-time">Ahora</div>
            </div>
        </div>

        <!-- Indicador de escritura -->
        <div class="typing-indicator" id="typingIndicator" style="display: none;">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>

        <!-- Área de entrada -->
        <div class="chat-input-container">
            <form id="chatForm" class="chat-input-form">
                <textarea 
                    id="userInput" 
                    class="chat-input" 
                    placeholder="Escribe tu mensaje aquí..."
                    rows="1"
                    maxlength="500"
                ></textarea>
                <button type="button" class="mic-button" id="micButton" title="Hablar por micrófono">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5-3c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                    </svg>
                </button>
                <button type="submit" class="send-button" id="sendButton" title="Enviar mensaje">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </form>
            <div class="input-footer">
                <small>Presiona Enter para enviar, Shift+Enter para nueva línea</small>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

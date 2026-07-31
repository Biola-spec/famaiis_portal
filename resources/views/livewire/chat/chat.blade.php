<div>
    <div class="content-wrapper" style="background-color: #f0f2f5 !important; min-height: 100vh;">
        <div class="container-full">
            <section class="content p-0">
                <div class="chat-app-container">
                    <div class="chat-sidebar">
                        @livewire('chat.chat-list')
                    </div>
                    <div class="chat-window">
                        @livewire('chat.chat-box')
                    </div>
                </div>
            </section>
        </div>
    </div>

    <style>
        :root {
            --wa-bg: #f0f2f5;
            --wa-header: #ededed;
            --wa-sidebar: #ffffff;
            --wa-message-sent: #dcf8c6;
            --wa-message-received: #ffffff;
            --wa-accent: #00a884;
            --wa-text: #111b21;
            --wa-text-secondary: #667781;
        }

        .chat-app-container {
            display: flex;
            flex-wrap: nowrap;
            height: 80vh;
            min-height: 500px;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            margin: 0 20px 20px 20px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }

        .chat-sidebar {
            width: 350px;
            flex-shrink: 0;
            border-right: 1px solid rgba(0,0,0,0.1);
            background: var(--wa-sidebar);
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar > div {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-window {
            flex: 1;
            background: #efe7de;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .chat-window > div {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }

        /* Dark Mode Overrides */
        .dark-skin .chat-app-container {
            background: #111b21;
            box-shadow: 0 6px 18px rgba(0,0,0,0.3);
        }
        .dark-skin .chat-sidebar {
            background: #111b21;
            border-right: 1px solid #222d34;
        }
        .dark-skin .chat-window {
            background: #0b141a;
        }
        .dark-skin .content-wrapper {
            background-color: #0c1317 !important;
        }

        @media (max-width: 991px) {
            .chat-sidebar {
                width: 100%;
                min-width: unset;
            }
            .chat-window {
                display: none;
            }
            .chat-app-container.show-chat .chat-sidebar {
                display: none;
            }
            .chat-app-container.show-chat .chat-window {
                display: flex;
                width: 100%;
            }
        }
    </style>
</div>

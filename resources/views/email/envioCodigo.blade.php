<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Acesso - DexLogo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #0a0e1a;
            color: #ffffff;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0a0e1a;
            padding: 40px 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo-text {
            font-size: 48px;
            font-weight: 700;
            color: #5b8def;
            letter-spacing: -1px;
        }
        .badge {
            display: inline-block;
            background-color: rgba(91, 141, 239, 0.1);
            border: 1px solid rgba(91, 141, 239, 0.3);
            border-radius: 20px;
            padding: 8px 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .badge-text {
            color: #5b8def;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            text-align: center;
            padding: 0 20px;
        }
        .title {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 20px;
            color: #ffffff;
        }
        .highlight {
            background: linear-gradient(135deg, #7c3aed 0%, #5b8def 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .description {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-bottom: 40px;
        }
        .code-container {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(91, 141, 239, 0.1) 100%);
            border: 2px solid rgba(91, 141, 239, 0.3);
            border-radius: 16px;
            padding: 40px;
            margin: 40px 0;
        }
        .code-label {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .code {
            font-size: 42px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .code-info {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
        }
        .warning {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 3px solid #ef4444;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 8px;
            text-align: left;
        }
        .warning-text {
            font-size: 14px;
            color: #fca5a5;
            line-height: 1.6;
        }
        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid rgba(91, 141, 239, 0.2);
            text-align: center;
        }
        .footer-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .footer-link {
            color: #5b8def;
            text-decoration: none;
        }
        .footer-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-text">DexLogo</div>
        </div>

        <!-- Badge -->
        <div style="text-align: center;">
            <div class="badge">
                <span class="badge-text">✨ IA Especializada em Logotipos</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1 class="title">
                Seu código de <span class="highlight">acesso único</span>
            </h1>

            <p class="description">
                Olá! Use o código abaixo para acessar sua conta DexLogo. 
                Este código é válido por tempo limitado e só pode ser usado uma vez.
            </p>

            <!-- Code Box -->
            <div class="code-container">
                <div class="code-label">Código de Acesso</div>
                <div class="code">{{ $codigo }}</div>
                <div class="code-info">Válido por 7 dias</div>
            </div>

            <!-- Warning -->
            <div class="warning">
                <div class="warning-text">
                    <strong>⚠️ Importante:</strong> Nunca compartilhe este código com ninguém. 
                    Nossa equipe nunca solicitará este código por telefone, email ou qualquer outro meio.
                </div>
            </div>

            <p class="description">
                Se você não solicitou este código, ignore este email ou 
                entre em contato conosco imediatamente.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Este é um email automático, por favor não responda.<br>
                © {{ date('Y') }} DexLogo - O primeiro gerador de logotipos criado para competir com agências<br>
            </p>
        </div>
    </div>
</body>
</html>
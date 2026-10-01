# Green River - Lead Capture Engine para Contratistas (USA)

Estrutura empresarial de alta conversão otimizada para tráfego móvel de **Facebook Ads**, segmentada para contratistas nos Estados Unidos (encanadores, telhadistas, técnicos de HVAC, eletricistas, homens de 40 a 60 anos).

Projetada especificamente para o ambiente de hospedagem **Hostinger Business (LiteSpeed/Apache + PHP puro)**, seguindo a arquitetura de segurança e separação de webroot da Green River Enterprise Core.

---

## 🏗️ Estrutura de Pastas e Arquitetura

```text
/Volumes/Jarvis/subdomain/
│
├── config/                               # 🔒 O COFRE (Fora do Webroot público)
│   └── config.php                        # Configurações confidenciais, e-mails e fuso horário
│
├── public_html/                          # 🌐 RAIZ DO SERVIDOR WEB (DocumentRoot da Hostinger)
│   ├── .htaccess                         # Otimizações de cache, gzip e cabeçalhos de segurança
│   ├── index.php                         # Roteador inteligente de idioma (detecta /es/ ou /en/)
│   ├── process.php                       # Processador de formulário seguro (carrega ../config/config.php)
│   │
│   ├── en/                               # 🇺🇸 Campanha em Inglês
│   │   ├── index.html                    # Página de captura de alta conversão
│   │   └── success.html                  # Página de confirmação e alinhamento de expectativa
│   │
│   ├── es/                               # 🇪🇸 Campanha em Espanhol (Contratistas hispânicos)
│   │   ├── index.html                    # Página de captura em espanhol
│   │   └── success.html                  # Página de confirmação em espanhol
│   │
│   └── assets/                           # 🎨 Recursos Estáticos Otimizados
│       ├── css/
│       │   └── style.css                 # CSS tátil de alto contraste (#121212 / #FFCC00)
│       ├── js/
│       │   └── main.js                   # Validação TCPA, máscara de telefone e proteção contra duplo clique
│       └── images/
│           └── og-preview.svg            # Card Open Graph em vetor para WhatsApp e Facebook Ads
│
└── README.md                             # Documentação técnica e guia de implantação
```

---

## 🔒 Benefícios da Nova Estrutura

1. **Separação de Segurança (O Cofre - `config/`)**:
   - O arquivo `config/config.php` reside um nível acima do webroot público (`public_html/`).
   - Mesmo que ocorra qualquer falha no interpretador PHP do servidor web, as credenciais e e-mails de destino nunca são expostos na internet.
2. **Compatibilidade Nativa com a Hostinger**:
   - Na Hostinger (hPanel), o DocumentRoot do subdomínio aponta diretamente para a pasta `public_html/`.
3. **URLs Limpas e Prontas para Tráfego Pago**:
   - Anúncios direcionados ao público hispânico: `https://promo.seudominio.com/es/`
   - Anúncios direcionados ao público de língua inglesa: `https://promo.seudominio.com/en/`
   - Acesso raiz (`https://promo.seudominio.com/`): identifica o idioma do dispositivo móvel e redireciona automaticamente via HTTP 302.
4. **Organização Modular de Assets**:
   - Arquivos CSS e JavaScript isolados em `assets/`, cacheados eficientemente pelo navegador e compactados via Gzip pelo `.htaccess`.
5. **Compatibilidade Retroativa**:
   - Apelidos de redirecionamento mantidos para URLs antigas como `index_en.html` e `index_es.html`.

---

## 🚀 Como Publicar na Hostinger

1. Acesse o **Gerenciador de Arquivos** ou conecte via **FTP / SSH** ao subdomínio da Hostinger.
2. Suba o diretório `config/` na raiz da conta (um nível antes de `public_html`).
3. Suba todo o conteúdo de `public_html/` para a pasta pública correspondente ao subdomínio.
4. Abra `config/config.php` e altere a constante `ADMIN_EMAIL` para o e-mail que receberá os leads:
   ```php
   define('ADMIN_EMAIL', 'seu-email@seudominio.com');
   ```
5. Realize um teste de envio de lead em `/en/` e em `/es/`. O e-mail será entregue de forma imediata e o usuário será redirecionado para a tela de confirmação correta com o alinhamento de expectativas.

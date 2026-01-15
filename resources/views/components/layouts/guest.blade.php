<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        :root {
            --primary: #FF385C;
            /* Airbnb-style vibrant red */
            --secondary: #222222;
            --accent: #FFD700;
            --bg-body: #F7F7F7;
            --card-radius: 24px;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --glass: rgba(255, 255, 255, 0.82);
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--secondary);
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        .app-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.05);
            padding-bottom: 120px;
            padding-top: 0;
        }

        .glass-nav {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow-md);
            margin: 10px 15px;
            padding: 12px 20px;
            position: sticky;
            top: 10px;
            z-index: 1000;
        }

        .header-bg {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%), var(--primary);
            min-height: 220px;
            padding-top: 1px;
            /* Prevent margin collapse */
            padding-bottom: 40px;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
        }

        .menu-item-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 12px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 1px solid #F0F0F0;
            cursor: pointer;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .menu-item-img {
            width: 100px;
            height: 100px;
            border-radius: 18px;
            object-fit: cover;
            box-shadow: var(--shadow-md);
        }

        .category-pill {
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-block;
            transition: 0.3s;
            text-decoration: none;
            color: #666;
            background: #F0F0F0;
            margin-right: 8px;
        }

        .category-pill.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 56, 92, 0.3);
        }

        .floating-action-button {
            position: fixed;
            bottom: 25px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 90%;
            max-width: 420px;
            background: var(--secondary);
            color: white;
            padding: 15px 20px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            z-index: 2500;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .floating-action-button:hover {
            transform: scale(1.02);
            color: white;
        }

        .item-quantity-badge {
            background: var(--primary);
            color: white;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 50px;
            position: absolute;
            top: -5px;
            right: -5px;
            border: 2px solid white;
        }

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .price-text {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.2rem;
        }

        .btn-add {
            background: var(--bg-body);
            border-radius: 14px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: 0.2s;
        }

        .btn-add:hover {
            background: var(--primary);
            color: white;
        }

        .status-dot {
            height: 10px;
            width: 10px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 360px) {
            .floating-action-button {
                padding: 12px 15px;
                bottom: 15px;
                width: calc(100% - 30px);
            }

            .floating-action-button span.fs-4 {
                font-size: 1.1rem !important;
            }

            .floating-action-button .bg-primary {
                width: 35px !important;
                height: 35px !important;
                margin-right: 10px !important;
            }

            .floating-action-button i.fs-2 {
                font-size: 1.2rem !important;
            }

            .menu-item-img {
                width: 80px;
                height: 80px;
            }

            .price-text {
                font-size: 1rem;
            }

            .btn-add {
                width: 32px;
                height: 32px;
            }

            .category-pill {
                padding: 8px 15px;
                font-size: 12px;
            }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @livewireStyles
</head>

<body>
    <div class="app-container shadow-none-sm">
        {{ $slot }}
    </div>

    <!-- JS dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @livewireScripts
</body>

</html>
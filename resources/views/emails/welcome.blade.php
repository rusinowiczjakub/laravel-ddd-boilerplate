@extends('emails.layouts.base')

@section('title', 'Witaj w Zen Arts!')

@section('header-title', 'Witaj w Zen Arts!')

@section('content')
    <p class="greeting">Cześć {{ $vendorName }}! 👋</p>

    <div class="message">
        <p>Cieszymy się, że dołączyłeś do społeczności twórców Zen Arts! Teraz możesz zacząć zarabiać na swoich projektach bez martwienia się o produkcję, wysyłkę czy obsługę klienta.</p>
    </div>

    <div class="info-box info-box-success">
        <p style="margin: 0; font-weight: 600; color: #166534;">🎨 Twoje konto jest aktywne!</p>
        <p style="margin: 8px 0 0 0; font-size: 14px; color: #15803d;">
            Możesz już dodawać swoje projekty i zarabiać pasywnie.
        </p>
    </div>

    <div class="message">
        <p><strong>Jak zacząć?</strong></p>
        <ol style="margin: 12px 0; padding-left: 24px; line-height: 1.8;">
            <li><strong>Przygotuj swój projekt</strong> - plik PSD, AI lub PNG w wysokiej rozdzielczości (min. 300 DPI)</li>
            <li><strong>Wgraj go do platformy</strong> - dodaj tytuł i wybierz kategorię</li>
            <li><strong>Poczekaj na weryfikację</strong> - zwykle trwa to 24-48 godzin</li>
            <li><strong>Zarabiaj!</strong> - po akceptacji Twój produkt jest dostępny 24/7</li>
        </ol>
    </div>

    <div style="text-align: center; margin: 32px 0;">
        <a href="{{ $dashboardUrl }}" class="button">Dodaj swój pierwszy projekt</a>
    </div>

    <div class="divider"></div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">70%</div>
            <div class="stat-label">Prowizja</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0 zł</div>
            <div class="stat-label">Koszt startu</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">24/7</div>
            <div class="stat-label">Sprzedaż</div>
        </div>
    </div>

    <div class="message">
        <p><strong>Przydatne linki:</strong></p>
        <ul style="list-style: none; margin: 12px 0; padding: 0;">
            <li style="margin: 8px 0;">
                📖 <a href="{{ $guidelinesUrl }}" style="color: #4F46E5; text-decoration: none;">Wytyczne dla designerów</a>
            </li>
            <li style="margin: 8px 0;">
                💡 <a href="{{ $tipsUrl }}" style="color: #4F46E5; text-decoration: none;">Jak tworzyć bestsellery</a>
            </li>
            <li style="margin: 8px 0;">
                ❓ <a href="{{ $faqUrl }}" style="color: #4F46E5; text-decoration: none;">Najczęściej zadawane pytania</a>
            </li>
            <li style="margin: 8px 0;">
                🤝 <a href="{{ $supportUrl }}" style="color: #4F46E5; text-decoration: none;">Pomoc i wsparcie</a>
            </li>
        </ul>
    </div>

    <div class="divider"></div>

    <div class="message">
        <p style="font-size: 14px; color: #64748b; text-align: center;">
            <strong>Masz pytania?</strong> Nasz zespół jest zawsze gotowy pomóc.<br>
            Po prostu odpowiedz na tego maila!
        </p>
    </div>
@endsection

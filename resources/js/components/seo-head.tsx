import { Head } from '@inertiajs/react';

interface SEOHeadProps {
    title: string;
    description: string;
    ogImage?: string;
    ogType?: 'website' | 'article';
    canonicalUrl?: string;
    keywords?: string;
    structuredData?: object;
}

export function SEOHead({
    title,
    description,
    ogImage = '/assets/og-image.png',
    ogType = 'website',
    canonicalUrl,
    keywords,
    structuredData
}: SEOHeadProps) {
    const appUrl = import.meta.env.VITE_APP_URL || (typeof window !== 'undefined' ? window.location.origin : '');
    const fullTitle = title;
    const url = canonicalUrl || (typeof window !== 'undefined' ? window.location.href : '');
    const fullOgImage = ogImage.startsWith('http') ? ogImage : `${appUrl}${ogImage}`;

    return (
        <Head>
            {/* Primary Meta Tags */}
            <title>{fullTitle}</title>
            <meta name="title" content={fullTitle} />
            <meta name="description" content={description} />
            {keywords && <meta name="keywords" content={keywords} />}

            {/* Canonical URL */}
            {canonicalUrl && <link rel="canonical" href={canonicalUrl} />}

            {/* Open Graph / Facebook */}
            <meta property="og:type" content={ogType} />
            <meta property="og:url" content={url} />
            <meta property="og:title" content={fullTitle} />
            <meta property="og:description" content={description} />
            <meta property="og:image" content={fullOgImage} />
            <meta property="og:site_name" content="Laravel Boilerplate" />

            {/* Twitter */}
            <meta property="twitter:card" content="summary_large_image" />
            <meta property="twitter:url" content={url} />
            <meta property="twitter:title" content={fullTitle} />
            <meta property="twitter:description" content={description} />
            <meta property="twitter:image" content={fullOgImage} />

            {/* Additional SEO Tags */}
            <meta name="robots" content="index, follow" />
            <meta name="language" content="English" />
            <meta name="revisit-after" content="7 days" />
            <meta name="author" content="Your Company" />

            {/* Structured Data */}
            {structuredData && (
                <script type="application/ld+json">
                    {JSON.stringify(structuredData)}
                </script>
            )}
        </Head>
    );
}

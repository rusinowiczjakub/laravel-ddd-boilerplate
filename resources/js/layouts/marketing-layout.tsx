import {MarketingFooter} from '@/components/marketing/footer';
import {MarketingNavbar} from '@/components/marketing/navbar';
import type {PropsWithChildren} from 'react';
import {SEOHead} from "@/components/seo-head";

const structuredData = {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Laravel Boilerplate",
    "description": "A production-ready foundation with DDD + CQRS architecture for building modern applications.",
    "applicationCategory": "DeveloperApplication",
    "operatingSystem": "Web",
    "author": {
        "@type": "Organization",
        "name": "Your Company"
    },
    "offers": {
        "@type": "Offer",
        "price": "0.00",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
    }
};

export default function MarketingLayout({children}: PropsWithChildren) {
    return (
        <>
            <SEOHead
                title={'DDD + CQRS Laravel Boilerplate'}
                description={'A production-ready foundation with Domain-Driven Design and CQRS architecture. Multi-tenancy, authentication, billing, and clean code out of the box.'}
                ogType={'website'}
                structuredData={structuredData}
            />

            <div
                className="flex min-h-screen flex-col overflow-x-clip bg-[#09090b] font-sans text-neutral-200 antialiased selection:bg-blue-500 selection:text-white">
                <MarketingNavbar/>

                <main className="w-full flex-1">{children}</main>

                <MarketingFooter/>
            </div>
        </>
    );
}

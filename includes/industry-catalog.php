<?php
/** Shared industry navigation; established URLs stay stable. */
function cbd_industry_catalog(): array
{
    return [
        'ecommerce' => ['slug' => 'ecommerce', 'name' => 'Retail & E-commerce', 'icon' => 'bi-shop', 'menu_class' => 'ind-ecommerce', 'description' => 'Stores & marketplaces', 'color' => 'from-emerald-400 to-teal-500'],
        'healthcare' => ['slug' => 'healthcare', 'name' => 'Healthcare', 'icon' => 'bi-heart-pulse', 'menu_class' => 'ind-healthcare', 'description' => 'Clinics & care providers', 'color' => 'from-rose-400 to-pink-500'],
        'fintech' => ['slug' => 'fintech', 'name' => 'FinTech', 'icon' => 'bi-bank', 'menu_class' => 'ind-startup', 'description' => 'Financial products & platforms', 'color' => 'from-violet-400 to-purple-500'],
        'education' => ['slug' => 'education', 'name' => 'Education & eLearning', 'icon' => 'bi-mortarboard', 'menu_class' => 'ind-education', 'description' => 'Learning & course platforms', 'color' => 'from-blue-400 to-indigo-500'],
        'travel' => ['slug' => 'travel', 'name' => 'Travel & Hospitality', 'icon' => 'bi-airplane', 'menu_class' => 'ind-travel', 'description' => 'Tours, stays & bookings', 'color' => 'from-sky-400 to-blue-500'],
        'real-estate' => ['slug' => 'real-estate', 'name' => 'Real Estate & PropTech', 'icon' => 'bi-house', 'menu_class' => 'ind-realestate', 'description' => 'Properties & real estate tools', 'color' => 'from-teal-400 to-cyan-500'],
        'industrial-manufacturing' => ['slug' => 'industrial-manufacturing', 'name' => 'Industrial & Manufacturing', 'icon' => 'bi-gear', 'menu_class' => 'ind-education', 'description' => 'Products, RFQs & operations', 'color' => 'from-yellow-400 to-orange-500'],
        'logistics-transportation' => ['slug' => 'logistics-transportation', 'name' => 'Logistics & Transportation', 'icon' => 'bi-truck', 'menu_class' => 'ind-travel', 'description' => 'Freight, fleets & deliveries', 'color' => 'from-sky-400 to-blue-500'],
        'news-portal' => ['slug' => 'news-portal', 'name' => 'Media & Entertainment', 'icon' => 'bi-newspaper', 'menu_class' => 'ind-news', 'description' => 'Publishing, content & events', 'color' => 'from-violet-400 to-purple-500'],
        'professional-services' => ['slug' => 'professional-services', 'name' => 'Business & Professional Services', 'icon' => 'bi-briefcase', 'menu_class' => 'ind-realestate', 'description' => 'Expertise, enquiries & clients', 'color' => 'from-teal-400 to-cyan-500'],
        'home-services' => ['slug' => 'home-services', 'name' => 'Home Services', 'icon' => 'bi-tools', 'menu_class' => 'ind-restaurant', 'description' => 'Local trades & appointments', 'color' => 'from-orange-400 to-red-500'],
        'automotive' => ['slug' => 'automotive', 'name' => 'Automotive', 'icon' => 'bi-car-front', 'menu_class' => 'ind-news', 'description' => 'Dealerships, parts & workshops', 'color' => 'from-blue-400 to-indigo-500'],
        'restaurant' => ['slug' => 'restaurant', 'name' => 'Food & Restaurants', 'icon' => 'bi-cup-hot', 'menu_class' => 'ind-restaurant', 'description' => 'Menus, orders & reservations', 'color' => 'from-yellow-400 to-orange-500'],
        'beauty-wellness' => ['slug' => 'beauty-wellness', 'name' => 'Beauty & Wellness', 'icon' => 'bi-flower1', 'menu_class' => 'ind-healthcare', 'description' => 'Salons, studios & bookings', 'color' => 'from-rose-400 to-pink-500'],
        'nonprofits-public-sector' => ['slug' => 'nonprofits-public-sector', 'name' => 'Nonprofits & Public Sector', 'icon' => 'bi-people', 'menu_class' => 'ind-education', 'description' => 'Programs & public information', 'color' => 'from-emerald-400 to-teal-500'],
        'startup' => ['slug' => 'startup', 'name' => 'Startups & SaaS', 'icon' => 'bi-rocket', 'menu_class' => 'ind-startup', 'description' => 'MVPs & subscription products', 'color' => 'from-orange-400 to-red-500'],
    ];
}

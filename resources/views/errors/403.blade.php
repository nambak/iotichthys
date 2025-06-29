<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>접근이 제한된 영역입니다</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/iotichthys_logo.png" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@300..700&family=Noto+Sans+KR:wght@100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/error.css') }}">
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
<div class="container bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6">
    <div class="ocean-bg">
        <div class="bubble bubble1"></div>
        <div class="bubble bubble2"></div>
        <div class="bubble bubble3"></div>
        <div class="bubble bubble4"></div>
        <div class="bubble bubble5"></div>
        <div class="seaweed seaweed1"></div>
        <div class="seaweed seaweed2"></div>
    </div>

    <div class="flex flex-col items-center justify-center gap-6">
        <img src="iotichthys_logo.png" style="width: 50%;" alt="Iotichthys logo">
        <h1 class="error-title">403</h1>
        <p class="text-white text-4xl">접근이 제한된 영역입니다</p>
        <p class="text-zinc-500">
            이 영역은 특별한 허가증이 필요한 보호구역입니다.<br>
            적절한 권한이 필요하거나 관리자에게 문의하세요.
        </p>
        <div class="error-actions">
            <a href="{{ url('/') }}" class="relative items-center font-medium justify-center gap-2 p-6 whitespace-nowrap disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-white border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)] *:transition-opacity [&[disabled]]:pointer-events-none w-full">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
                홈으로 돌아가기
            </a>
        </div>
    </div>
</div>
</body>
</html>

<?php

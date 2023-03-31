<!DOCTYPE html>
<html lang="en" {if language_rtl}dir="rtl"{/if}>

<head>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="{system_site_desc|truncate:165}">
    <link rel="icon" href="{get_image("favicon")}">

    <title>{$title} &middot; {system_site_name}</title>

    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Rubik+Mono+One&display=swap">
    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="{_assets("css/libs/line-awesome.min.css")}">
    <link rel="stylesheet" href="{_assets("css/libs/flag-icon.min.css")}">
    <link rel="stylesheet" href="{if language_rtl}{assets("css/libs/bootstrap.rtl.min.css")}{else}{assets("css/libs/bootstrap.min.css")}{/if}">
    <link rel="stylesheet" href="{if language_rtl}{assets("css/style.rtl.min.css")}{else}{assets("css/style.min.css")}{/if}">

    <style>
        body {
            background: url({get_image("bg")}) top center;
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>

<body>
    {if logged_id && !in_array($page, ["misc", "login", "register", "forgot"])}
    {include "./modules/header.block.tpl"}
    {/if}
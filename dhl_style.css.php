<?php
// This file contains DHL-specific CSS that can be included in all pages
?>
<style>
/* DHL Color Palette */
:root {
    --dhl-red: #D40511;
    --dhl-yellow: #FFCC00;
    --dhl-dark: #000000;
    --dhl-light: #FFFFFF;
    --dhl-gray: #666666;
    --dhl-light-gray: #F5F5F5;
}

/* DHL Button Styles */
.dhl-btn {
    background: linear-gradient(135deg, var(--dhl-yellow) 0%, var(--dhl-red) 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dhl-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(212, 5, 17, 0.3);
}

/* DHL Card Styles */
.dhl-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-top: 4px solid var(--dhl-red);
}

.dhl-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* DHL Badge */
.dhl-badge {
    display: inline-block;
    background: var(--dhl-red);
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* DHL Alert */
.dhl-alert {
    background: linear-gradient(135deg, var(--dhl-yellow) 0%, var(--dhl-red) 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    margin: 20px 0;
    border-left: 4px solid var(--dhl-dark);
}

/* DHL Navigation */
.dhl-nav-item {
    color: var(--dhl-dark);
    text-decoration: none;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 4px;
    transition: all 0.3s ease;
    position: relative;
}

.dhl-nav-item:hover {
    color: var(--dhl-red);
    background: rgba(212, 5, 17, 0.1);
}

.dhl-nav-item.active {
    color: var(--dhl-red);
    background: rgba(212, 5, 17, 0.1);
    border-bottom: 3px solid var(--dhl-yellow);
}

/* DHL Typography */
.dhl-heading {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    font-weight: 700;
    color: var(--dhl-dark);
    line-height: 1.3;
}

.dhl-subheading {
    color: var(--dhl-gray);
    font-size: 1.1rem;
    font-weight: 500;
}

/* DHL Form Elements */
.dhl-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
}

.dhl-input:focus {
    outline: none;
    border-color: var(--dhl-red);
    box-shadow: 0 0 0 3px rgba(212, 5, 17, 0.1);
}

/* DHL Table */
.dhl-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.dhl-table th {
    background: var(--dhl-red);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.dhl-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.dhl-table tr:hover {
    background: rgba(212, 5, 17, 0.05);
}

/* DHL Loading Spinner */
.dhl-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(212, 5, 17, 0.1);
    border-left-color: var(--dhl-red);
    border-radius: 50%;
    animation: dhl-spin 1s linear infinite;
}

@keyframes dhl-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* DHL Progress Bar */
.dhl-progress {
    height: 8px;
    background: #eee;
    border-radius: 4px;
    overflow: hidden;
    margin: 20px 0;
}

.dhl-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--dhl-yellow), var(--dhl-red));
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* DHL Quote */
.dhl-quote {
    border-left: 4px solid var(--dhl-yellow);
    padding-left: 20px;
    margin: 30px 0;
    font-style: italic;
    color: var(--dhl-gray);
    background: rgba(212, 5, 17, 0.05);
    padding: 20px;
    border-radius: 0 8px 8px 0;
}
</style>

<?php
// Generate placeholder thumbnails for DHL News
$thumbnails = [
    'dhl_asia_europe.jpg' => ['#D40511', '#FFCC00', '✈️'],
    'supply_chain.jpg' => ['#9370DB', '#8A2BE2', '🔗'],
    'electric_fleet.jpg' => ['#228B22', '#32CD32', '🚗'],
    'ai_customs.jpg' => ['#8B4513', '#D2691E', '🤖'],
    'global_trade.jpg' => ['#0066B3', '#1E90FF', '🌍'],
    'automated_facility.jpg' => ['#FF4500', '#FF8C00', '🏭'],
    'carbon_neutral.jpg' => ['#228B22', '#20B2AA', '🌱'],
    'ecommerce.jpg' => ['#FFCC00', '#FFD700', '🛒'],
    'drone_delivery.jpg' => ['#1E90FF', '#87CEEB', '🚁'],
    'air_cargo.jpg' => ['#4169E1', '#6495ED', '📦'],
];

foreach ($thumbnails as $filename => $colors) {
    $width = 400;
    $height = 220;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Create gradient
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = intval(hexdec(substr($colors[0], 1, 2)) * (1 - $ratio) + hexdec(substr($colors[1], 1, 2)) * $ratio);
        $g = intval(hexdec(substr($colors[0], 3, 2)) * (1 - $ratio) + hexdec(substr($colors[1], 3, 2)) * $ratio);
        $b = intval(hexdec(substr($colors[0], 5, 2)) * (1 - $ratio) + hexdec(substr($colors[1], 5, 2)) * $ratio);
        
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }
    
    // Add icon/text (using GD)
    $textColor = imagecolorallocate($image, 255, 255, 255);
    $font = 5; // Built-in GD font
    
    // Center icon/text
    $icon = $colors[2];
    $iconSize = 60;
    
    // Save image
    imagejpeg($image, 'thumbnails/' . $filename, 90);
    imagedestroy($image);
    
    echo "Generated: $filename\n";
}

echo "All thumbnails generated!\n";
?>

document.addEventListener('DOMContentLoaded', function () {
    const hexagons = document.querySelectorAll('.hexagon');

    hexagons.forEach(hex => {
        hex.addEventListener('mouseenter', function () {
            this.style.filter = 'brightness(1.2) saturate(1.1)';
        });

        hex.addEventListener('mouseleave', function () {
            this.style.filter = 'brightness(1) saturate(1)';
        });
    });

    // Parallax effect bij scroll
    window.addEventListener('scroll', function () {
        const scrolled = window.pageYOffset;
        const hexagons = document.querySelectorAll('.hexagon');

        hexagons.forEach((hex, index) => {
            const speed = 0.5 + (index * 0.1);
            hex.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });

    // Willekeurige animatie triggers
    setInterval(() => {
        const randomHex = hexagons[Math.floor(Math.random() * hexagons.length)];
        randomHex.style.animation = 'none';
        setTimeout(() => {
            randomHex.style.animation = '';
        }, 100);
    }, 3000);
});
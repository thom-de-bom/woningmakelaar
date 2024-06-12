document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const navLinks = {
        'navHome': 0,
        'navWoningen': 1,
        'navContact': 2,
        'navAbout': 3
    };
    let currentSlide = 0;
    let isAnimating = false; // Track if an animation is currently running

    const getAnimationClass = (index) => {
        switch (index) {
            case 0: return 'slide-from-top';
            case 1: return 'slide-from-right';
            case 2: return 'slide-from-bottom';
            case 3: return 'slide-from-left';
            default: return '';
        }
    };

    const getReverseAnimationClass = (index) => {
        switch (index) {
            case 0: return 'slide-to-bottom';
            case 1: return 'slide-to-left';
            case 2: return 'slide-to-top';
            case 3: return 'slide-to-right';
            default: return '';
        }
    };

    const showSlide = (index) => {
        if (index >= 0 && index < slides.length && index !== currentSlide && !isAnimating) {
            isAnimating = true; // Set animation flag
            const incomingAnimationClass = getAnimationClass(index);
            const outgoingAnimationClass = getReverseAnimationClass(currentSlide);
    
            slides[currentSlide].classList.add('hide-scrollbars', outgoingAnimationClass);
            slides[index].classList.add('hide-scrollbars', 'slide-active', incomingAnimationClass);
    
            setTimeout(() => {
                slides[currentSlide].classList.remove('slide-active', outgoingAnimationClass, 'hide-scrollbars');
                slides[index].classList.remove(incomingAnimationClass, 'hide-scrollbars');
                currentSlide = index;
                isAnimating = false; // Reset animation flag
            }, 700); // Adjust duration to match animation
        }
    };
    
    Object.keys(navLinks).forEach(navId => {
        document.getElementById(navId).addEventListener('click', () => {
            showSlide(navLinks[navId]);
        });
    });

    slides[0].classList.add('slide-active', getAnimationClass(0));
});

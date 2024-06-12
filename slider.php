<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Slider</title>    
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body, html {
    margin: 0;
    padding: 0;
    overflow: hidden;
}

#slider {
    white-space: nowrap;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    width: 100vw;
    height: 100vh;
}

.slide {
    width: 100vw;
    height: 100vh;
    position: absolute;
    top: 0;
    left: 0;
    display: none;
}
 
.slidertog {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    padding: 10px 20px;
    cursor: pointer;
}

#prevSlide {
    left: 10px;
}

#nextSlide {
    right: 10px;
}

.slide-active {
    display: block;
}

.slide-from-top {
    animation: slideFromTop 0.5s ease forwards;
}

.slide-from-bottom {
    animation: slideFromBottom 0.5s ease forwards;
}

.slide-from-left {
    animation: slideFromLeft 0.5s ease forwards;
}

.slide-from-right {
    animation: slideFromRight 0.5s ease forwards;
}

@keyframes slideFromTop {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

@keyframes slideFromBottom {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

@keyframes slideFromLeft {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

@keyframes slideFromRight {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

    </style>
</head>
<body>
<header>
    <div class="container">
        <a href="index.php"><img class="logo" src="./assets/img/Vrijwonen_makelaar.png" alt="logo"></a>
        <div class="nav">
            <a href="./woningen.php">woningen</a>
            <a href="contact.php">contact</a>
            <a href="./about.php">about</a>
        </div>
        <?php
        include "./assets/php/header.php"
        ?>
    </div>
</header>

    <div id="slider">
        <div class="slide" style="background-color: red;">
        <p>test1</p>
        </div>
        <div class="slide" style="background-color: green;">
        <p>test2</p>
        </div>
        <div class="slide" style="background-color: blue;">
        <p>test3</p>
        </div>
        <div class="slide" style="background-color: yellow;">
        <p>test4</p>
        </div>
    </div>

    <button class="slidertog" id="prevSlide">Previous</button>
    <button class="slidertog" id="nextSlide">Next</button>

    <script>
 document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;
    let isInitialLoad = true; // Flag to check if it's the initial page load

    const showSlide = (index) => {
        if (index >= 0 && index < slides.length) {
            if (currentSlide >= 0 && currentSlide < slides.length) {
                slides[currentSlide].classList.remove('slide-active', 'slide-from-top', 'slide-from-right', 'slide-from-bottom', 'slide-from-left');
            }

            if (!isInitialLoad) {
                switch (index) {
                    case 0:
                        slides[index].classList.add('slide-from-top');
                        break;
                    case 1:
                        slides[index].classList.add('slide-from-right');
                        break;
                    case 2:
                        slides[index].classList.add('slide-from-bottom');
                        break;
                    case 3:
                        slides[index].classList.add('slide-from-left');
                        break;
                }
            }

            slides[index].classList.add('slide-active');
            currentSlide = index;
            isInitialLoad = false; // Set to false after the first load
        }
    };

    document.getElementById('prevSlide').addEventListener('click', () => {
        showSlide(currentSlide - 1);
    });

    document.getElementById('nextSlide').addEventListener('click', () => {
        showSlide(currentSlide + 1);
    });

    // Initialize the first slide as active
    showSlide(0);
});


    </script> <!-- Ensure the correct path to your JavaScript file -->
</body>
</html>

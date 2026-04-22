class Slider {
    constructor(container, options = {}) {
        this.container = container;
        this.slider = container.querySelector('.slider');
        this.slides = container.querySelectorAll('.slide');
        this.prevBtn = container.querySelector('.prev');
        this.nextBtn = container.querySelector('.next');
        this.dotsContainer = container.querySelector('.slider-dots');
        
        this.currentIndex = 0;
        this.slideCount = this.slides.length;
        this.autoPlayInterval = options.autoPlayInterval || 3000;
        this.autoPlay = options.autoPlay !== false;
        
        this.init();
    }
    
    init() {
        // Создаем точки
        this.createDots();
        
        // Обработчики
        this.prevBtn.addEventListener('click', () => this.prev());
        this.nextBtn.addEventListener('click', () => this.next());
        
        // Автопереключение
        if (this.autoPlay) {
            this.startAutoPlay();
            this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
            this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        }
        
        // Свайпы для мобильных
        this.initSwipe();
    }
    
    createDots() {
        for (let i = 0; i < this.slideCount; i++) {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => this.goToSlide(i));
            this.dotsContainer.appendChild(dot);
        }
    }
    
    updateDots() {
        const dots = this.dotsContainer.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === this.currentIndex);
        });
    }
    
    goToSlide(index) {
        this.currentIndex = index;
        this.slider.style.transform = `translateX(-${this.currentIndex * 100}%)`;
        this.updateDots();
    }
    
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.slideCount;
        this.goToSlide(this.currentIndex);
    }
    
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.slideCount) % this.slideCount;
        this.goToSlide(this.currentIndex);
    }
    
    startAutoPlay() {
        this.autoPlayIntervalId = setInterval(() => this.next(), this.autoPlayInterval);
    }
    
    stopAutoPlay() {
        clearInterval(this.autoPlayIntervalId);
    }
    
    initSwipe() {
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.container.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        this.container.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe(touchStartX, touchEndX);
        });
    }
    
    handleSwipe(start, end) {
        const diff = start - end;
        if (Math.abs(diff) > 50) {
            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
        }
    }
}

// Автоинициализация
document.addEventListener('DOMContentLoaded', () => {
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        new Slider(sliderContainer, { autoPlayInterval: 3000 });
    }
});
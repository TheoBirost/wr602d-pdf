import { gsap } from "gsap";

function animate(element, animationProps) {
    gsap.from(element, animationProps);
}

document.addEventListener('DOMContentLoaded', () => {
    const animation = {
        opacity: 0,
        y: 20,
        duration: 0.5,
        stagger: 0.1
    };

    document.querySelectorAll('[data-animation]').forEach(el => {
        animate(el, animation);
    });
});


// Select all elements with the 'cr-animated' class
const animatedElements = Array.from(document.querySelectorAll('.cr-animated'));

// Define the intersection handler function
const handleIntersection = (entries, observer) => {
    entries.forEach((entry) => {
        // Check if the element is intersecting with the viewport
        if (entry.isIntersecting) {
            // Extract animation classes from the target element's class list
            const animationClasses = entry.target.classList
                .toString()
                .split(" ")
                .filter((className) => className.startsWith("animate__"))
                .map((animation) => animation.replace("animate__", ""));

            // Add extracted animation classes to the target element
            entry.target.classList.add(...animationClasses);

            // Stop observing the element once animations are applied
            observer.unobserve(entry.target);
        }
    });
};

// Check if there are elements with the 'cr-animated' class
if (animatedElements.length > 0) {
    // Create an Intersection Observer with the intersection handler function
    const intersectionObserver = new IntersectionObserver(handleIntersection);

    // Observe each animated element
    animatedElements.forEach((element) => intersectionObserver.observe(element));
}

document.addEventListener("DOMContentLoaded", () => {
  // 1. Sticky Navbar Effect
  const navbar = document.querySelector(".navbar");

  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled", "shadow-sm", "fixed-top");
    } else {
      navbar.classList.remove("scrolled", "shadow-sm", "fixed-top");
    }
  });

  const revealElements = document.querySelectorAll(".reveal");

  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      root: null,
      threshold: 0.15,
      rootMargin: "0px",
    },
  );
  revealElements.forEach((el) => revealObserver.observe(el));
  console.log("Design By Mahtab!!");
  const counters = document.querySelectorAll(".counter");
  const speed = 200;

  const startCounting = (counter) => {
    const target = +counter.getAttribute("data-target");
    const increment = target / speed;

    const updateCount = () => {
      const count = +counter.innerText;

      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(updateCount, 15);
      } else {
        counter.innerText = target + "+";
      }
    };

    updateCount();
  };

  const statsObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          startCounting(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.5 },
  );

  counters.forEach((counter) => {
    statsObserver.observe(counter);
  });
  const heroBg = document.getElementById("heroBg");
  if (heroBg) {
    window.addEventListener("scroll", () => {
      const scrollPos = window.scrollY;
      if (scrollPos < window.innerHeight) {
        heroBg.style.transform = `translateY(${scrollPos * 0.4}px)`;
      }
    });
  }

  /* Contact Form */
  const contactForm = document.getElementById("contactForm");
  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Thank you! Your message has been sent to Vijay Automobiles.");
      contactForm.reset();
    });
  }
});

document.getElementById("contactForm").addEventListener("submit", function(e) {
  e.preventDefault();

  let name = this.querySelector("input[type='text']").value;
  let email = this.querySelector("input[type='email']").value;
  let message = this.querySelector("textarea").value;

  if(name && email && message){
    alert("Message Sent Successfully ✅");
    this.reset();
  } else {
    alert("Please fill all fields ❌");
  }
});

document.getElementById("contactForm").addEventListener("submit", function(e) {
  e.preventDefault();

  let name = this.querySelector("input[type='text']").value;
  let email = this.querySelector("input[type='email']").value;
  let message = this.querySelector("textarea").value;

  if(name && email && message){
    alert("Message Sent Successfully ✅");
    this.reset();
  } else {
    alert("Please fill all fields ❌");
  }
});
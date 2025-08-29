document.addEventListener("DOMContentLoaded", () => {
  initBannerSlider();
});

// Banner Slider Functionality
function initBannerSlider() {
  const slider = document.querySelector("#slider-hero");
  const slides = slider?.querySelectorAll("[id^='slide-hero-']");
  const navDots = document.querySelectorAll(
    "#slider-hero-nav button"
  );

  if (!slider || !slides || !navDots) return;

  const config = {
    autoSlideInterval: 3000,
    activeColor: "#E0FE10",
    inactiveColor: "#FFFFFF",
  };

  let currentIndex = 0;
  let autoSlideTimer;

  const updateNavDots = () => {
    navDots.forEach((dot, index) => {
      const isActive = index === currentIndex;

      dot.style.width = isActive ? "25px" : "5px";
      dot.style.opacity = isActive ? "1" : "0.75";
      dot.style.backgroundColor = isActive
        ? config.activeColor
        : config.inactiveColor;
      dot.style.transition = "all 0.3s ease";
    });
  };

  const scrollToSlide = (index, smooth = true) => {
    currentIndex = index;
    slider.scrollTo({
      left: slider.clientWidth * currentIndex,
      behavior: smooth ? "smooth" : "auto",
    });
    updateNavDots();
  };

  const startAutoSlide = () => {
    autoSlideTimer = setInterval(() => {
      currentIndex = (currentIndex + 1) % slides.length;
      scrollToSlide(currentIndex);
    }, config.autoSlideInterval);
  };

  const stopAutoSlide = () => {
    clearInterval(autoSlideTimer);
  };

  // Event listeners
  navDots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      stopAutoSlide();
      scrollToSlide(index);
      startAutoSlide();
    });
  });

  slider.addEventListener("scroll", () => {
    const scrollLeft = slider.scrollLeft;
    const slideWidth = slider.clientWidth;
    const newIndex = Math.round(scrollLeft / slideWidth);

    if (newIndex !== currentIndex) {
      currentIndex = newIndex;
      updateNavDots();
    }
  });

  // Initialize
  updateNavDots();
  startAutoSlide();
}

// Video player functionality
const video = document.getElementById("video-trigger");
const playButton = document.getElementById("video-play-button");
const blurCanvas = document.getElementById("blur-canvas");
const progressBar = document.getElementById("progress-bar");
const progressInput = document.getElementById("progress-input");
const currentTimeEl = document.getElementById("current-time");
const durationTimeEl = document.getElementById("duration-time");

// Format time helper function
const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return `${mins.toString().padStart(2, "0")}:${secs
    .toString()
    .padStart(2, "0")}`;
};

// Setup video progress functionality
if (video && progressBar && progressInput && currentTimeEl && durationTimeEl) {
  // Update progress bar and time display
  const updateProgress = () => {
    if (video.duration) {
      const progress = (video.currentTime / video.duration) * 100;
      progressBar.style.width = `${progress}%`;
      progressInput.value = progress.toString();
      currentTimeEl.textContent = formatTime(video.currentTime);
    }
  };

  // Set duration when video metadata is loaded
  video.addEventListener("loadedmetadata", () => {
    durationTimeEl.textContent = formatTime(video.duration);
  });

  // Update progress during playback
  video.addEventListener("timeupdate", updateProgress);

  // Handle progress bar click/drag
  progressInput.addEventListener("input", (e) => {
    const target = e.target;
    const clickTime = (parseFloat(target.value) / 100) * video.duration;
    video.currentTime = clickTime;
  });

  // Prevent progress input from triggering video controls
  progressInput.addEventListener("click", (e) => {
    e.stopPropagation();
  });
}

// Setup blur canvas background effect
if (video && blurCanvas) {
  const ctx = blurCanvas.getContext("2d");

  const setupBlurEffect = () => {
    // Set canvas size to match container
    const container = blurCanvas.parentElement;
    if (container) {
      blurCanvas.width = container.clientWidth;
      blurCanvas.height = container.clientHeight;
    }

    const renderBlurFrame = () => {
      if (ctx && video.videoWidth > 0 && video.videoHeight > 0) {
        // Clear canvas
        ctx.clearRect(0, 0, blurCanvas.width, blurCanvas.height);

        // Set blur filter
        ctx.filter = "blur(8px)";

        // Calculate dimensions to fill canvas (object-cover behavior)
        const videoAspect = video.videoWidth / video.videoHeight;
        const canvasAspect = blurCanvas.width / blurCanvas.height;

        let drawWidth, drawHeight, drawX, drawY;

        if (videoAspect > canvasAspect) {
          // Video is wider - fit height and crop width
          drawHeight = blurCanvas.height * 1.1; // Scale up 10% like CSS scale-110
          drawWidth = drawHeight * videoAspect;
          drawX = (blurCanvas.width - drawWidth) / 2;
          drawY = (blurCanvas.height - drawHeight) / 2;
        } else {
          // Video is taller - fit width and crop height
          drawWidth = blurCanvas.width * 1.1; // Scale up 10% like CSS scale-110
          drawHeight = drawWidth / videoAspect;
          drawX = (blurCanvas.width - drawWidth) / 2;
          drawY = (blurCanvas.height - drawHeight) / 2;
        }

        // Draw video frame with blur
        ctx.drawImage(video, drawX, drawY, drawWidth, drawHeight);
      }

      // Continue animation
      requestAnimationFrame(renderBlurFrame);
    };

    // Start rendering when video is ready
    video.addEventListener("loadeddata", () => {
      renderBlurFrame();
    });

    // If video is already loaded
    if (video.readyState >= 2) {
      renderBlurFrame();
    }
  };

  // Handle window resize
  const handleResize = () => {
    const container = blurCanvas.parentElement;
    if (container) {
      blurCanvas.width = container.clientWidth;
      blurCanvas.height = container.clientHeight;
    }
  };

  window.addEventListener("resize", handleResize);
  setupBlurEffect();
}

// Video controls
if (video && playButton) {
  video.addEventListener("click", () => {
    if (!video.paused) {
      playButton.style.visibility = "visible";
      video.pause();
    } else {
      playButton.style.visibility = "hidden";
      video.play();
    }
  });

  playButton.addEventListener("click", () => {
    if (video.paused) {
      playButton.style.visibility = "hidden";
      video.play();
    }
  });
}

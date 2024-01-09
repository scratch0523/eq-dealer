document.addEventListener("DOMContentLoaded", function () {
  const imageList = document.getElementById("imageList");
  const nonModelCarousel = document.querySelector(".sliider__banner__carousel");
  const nonModelArrowBtns = document.querySelectorAll(".slider__banner i");
  const zoomContainer = document.querySelector(".zoom-container");
  let zoomImage = document.getElementById("zoomImage");
  let currentId = "Image_1";
  const firstCardWidth = nonModelCarousel.querySelector(
    ".slider__banner__carousel__card"
  ).offsetWidth;
  let childElements = document
    .getElementById("imageList")
    .getElementsByTagName("li");
  window.onload = function () {
    generateImageIds();
  };

  /*----- Slider ------*/

  const nonModelCarouselChildrens = [...nonModelCarousel.children];

  let isDragging = false,
    startX,
    startScrollLeft;

  nonModelArrowBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      nonModelCarousel.scrollLeft +=
        btn.id === "left" ? -firstCardWidth : firstCardWidth;
    });
  });

  const dragStart = (e) => {
    isDragging = true;
    nonModelCarousel.classList.add("dragging");
    startX = e.pageX;
    startScrollLeft = nonModelCarousel.scrollLeft;
  };

  const dragging = (e) => {
    if (!isDragging) return;
    nonModelCarousel.scrollLeft = startScrollLeft - (e.pageX - startX);
  };

  const dragStop = () => {
    isDragging = false;
    nonModelCarousel.classList.remove("dragging");
  };

  nonModelCarousel.addEventListener("mousedown", dragStart);
  nonModelCarousel.addEventListener("mousemove", dragging);
  document.addEventListener("mouseup", dragStop);

  /*____________ Hover to Zoom _______________*/

  zoomContainer.addEventListener("mousemove", handleMouseMove);
  zoomContainer.addEventListener("mouseleave", resetZoom);

  function handleMouseMove(e) {
    const { offsetX, offsetY } = e;
    const { offsetWidth, offsetHeight } = zoomContainer;

    const xPercentage = (offsetX / offsetWidth) * 100;
    const yPercentage = (offsetY / offsetHeight) * 100;

    zoomImage.style.transformOrigin = `${xPercentage}% ${yPercentage}%`;
    zoomImage.classList.add("zoomed");
  }

  function resetZoom() {
    zoomImage.style.transformOrigin = "50% 50%";
    zoomImage.classList.remove("zoomed");
  }

  /*--------------- Click to Slider Image Change ---------------*/
  imageList.addEventListener("click", function (event) {
    const clickedElement = event.target;
    const clickedElementID = event.target.src;
    currentId = clickedElementID;
    zoomImage.src = currentId;
  });

  function incrementImageId(currentId) {
    const numericPart = parseInt(currentId.split("_")[1]);
    const firstIncrementId = "Image_" + (numericPart + 1);
    setNewId(firstIncrementId);
  }

  function setNewId(newId) {
    const newIdNumericValue = parseInt(newId.split("_")[1]);
    if (childElements.length >= newIdNumericValue) {
      currentId = newId;
    }
    return currentId;
  }

  function decrementImageId(currentId) {
    const numericPart = parseInt(currentId.split("_")[1]);
    const firstIncrementId = "Image_" + (numericPart - 1);
    setNewId(firstIncrementId);
  }

  nonModelArrowBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.id === "right"
        ? incrementImageId(currentId)
        : decrementImageId(currentId);
      const imageChangeID = currentId;
      const getImagePath = document.querySelector(`#${imageChangeID}`).src;

      setImagePath(getImagePath);
    });
  });

  function setImagePath(getImagePath) {
    const oldImage = document.querySelector("#zoomImage");
    oldImage.src = getImagePath;
  }
  function generateImageIds() {
    const imgElements = imageList.getElementsByTagName("img");
    for (let i = 0; i < imgElements.length; i++) {
      imgElements[i].setAttribute("id", `Image_${i + 1}`);
    }
  }
});

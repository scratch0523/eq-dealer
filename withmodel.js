document.addEventListener("DOMContentLoaded", function () {
  const imageList = document.querySelector("#imageList__model");
  const globalCarousel = document.querySelector(
    ".sliider__banner__carousel__model"
  );
  const arrowBtns = document.querySelectorAll(".slider__banner__model i");
  const zoomContainer = document.querySelector(".zoom-container__model");
  let zoomImage = document.getElementById("zoomImage__model");
  let currentId = "ImageModel_1";
  const firstCardWidth = globalCarousel.querySelector(
    ".slider__banner__carousel__card__model"
  ).offsetWidth;
  let childElements = document
    .getElementById("imageList__model")
    .getElementsByTagName("li");

  function generateImageIds() {
    const imgElements = imageList.getElementsByTagName("img");
    for (let i = 0; i < imgElements.length; i++) {
      imgElements[i].setAttribute("id", `ImageModel_${i + 1}`);
    }
  }

  function handleCarouselButton(btn) {
    globalCarousel.scrollLeft +=
      btn.id === "left__model" ? -firstCardWidth : firstCardWidth;
  }

  function handleMouseMoveEvent(e) {
    const {
      offsetX,
      offsetY
    } = e;
    const {
      offsetWidth,
      offsetHeight
    } = zoomContainer;
    const xPercentage = (offsetX / offsetWidth) * 100;
    const yPercentage = (offsetY / offsetHeight) * 100;
    zoomImage.style.transformOrigin = `${xPercentage}% ${yPercentage}%`;
    zoomImage.classList.add("zoomed__model");
  }

  function resetZoomState() {
    zoomImage.style.transformOrigin = "50% 50%";
    zoomImage.classList.remove("zoomed__model");
  }

  function handleImageClickEvent(event) {
    const clickedElementID = event.target.id;
    currentId = clickedElementID;
    zoomImage.src = event.target.src;
  }

  function changeImageId(increment) {
    const numericPart = parseInt(currentId.split("_")[1]);
    const newNumericPart = increment ? numericPart + 1 : numericPart - 1;
    const newId = `ImageModel_${newNumericPart}`;
    if (childElements.length >= newNumericPart) {
      currentId = newId;
    }
  }

  arrowBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      handleCarouselButton(btn);
      changeImageId(btn.id === "right__model");
      setImage();
    });
  });

  imageList.addEventListener("click", handleImageClickEvent);

  zoomContainer.addEventListener("mousemove", handleMouseMoveEvent);
  zoomContainer.addEventListener("mouseleave", resetZoomState);

  window.onload = function () {
    generateImageIds();
  };

  function setImage() {
    zoomImage.src = document.getElementById(currentId).src;
  }
});
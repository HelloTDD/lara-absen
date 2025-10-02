var TDDUtil = {
  onDOMContentLoaded: function (callback) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", callback);
    } else {
      callback();
    }
  },
};

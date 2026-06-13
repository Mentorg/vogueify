export function useProductForm({ colors = [] }) {
  const addFeature = (form) => {
    form.features.push({
      title: '',
      description: ''
    });
  };

  const removeFeature = (form, index) => {
    form.features.splice(index, 1)
  };

  const toggleCollapse = (form, index) => {
    form.variations[index].collapsed = !form.variations[index].collapsed;
  };

  const getColorHex = (colorId) => {
    const color = colors.find(
      (c) => String(c.id) === String(colorId)
    );

    if (!color?.hex_code) {
      return "#000000";
    }

    return color.hex_code.startsWith("#")
      ? color.hex_code
      : `#${color.hex_code}`;
  };

  const preventDecimal = (e) => {
    if (e.key === "." || e.key === ",") {
      e.preventDefault();
    }
  };

  const sanitizeInteger = (value) => {
    const parsed = Number(value);

    if (Number.isNaN(parsed)) {
      return 0;
    }

    return Math.max(0, Math.floor(parsed));
  };

  return {
    addFeature,
    removeFeature,
    toggleCollapse,
    getColorHex,
    preventDecimal,
    sanitizeInteger,
  }
}

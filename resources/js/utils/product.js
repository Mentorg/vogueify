export const isOutOfStock = (variation) => {
  if (!variation) return true

  if (Array.isArray(variation.sizes) && variation.sizes.length > 0) {
    return variation.sizes.every(size => Number(size.pivot?.stock ?? 0) <= 0)
  }

  return Number(variation.stock) <= 0
}

export const getProductsOutOfStockMap = (products) => {
  if (!Array.isArray(products)) return {}

  return products.reduce((acc, product) => {
    acc[product.id] = isOutOfStock(product)
    return acc
  }, {})
}

export const preventDecimal = (e) => {
  if (['.', ',', 'e', 'E', '-', '+'].includes(e.key)) {
    e.preventDefault()
  }
}

export const getPreferredSizeSystem = () => {
  const locale = navigator.language;

  const europeanCountries = [
    'GB', 'DE', 'FR', 'IT', 'ES', 'CH', 'AT', 'PL',
    'SE', 'NO', 'FI', 'IE', 'NL', 'BE', 'DK',
    'CZ', 'XK'
  ];

  const country = locale.split('-')[1]?.toUpperCase();

  return europeanCountries.includes(country) ? 'EU' : 'US';
}

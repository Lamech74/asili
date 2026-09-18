const currencyByRegion: Record<string, string> = {
  AE: 'AED',
  AU: 'AUD',
  CA: 'CAD',
  CN: 'CNY',
  DE: 'EUR',
  ES: 'EUR',
  FR: 'EUR',
  GH: 'GHS',
  GB: 'GBP',
  IN: 'INR',
  JP: 'JPY',
  KE: 'KES',
  NG: 'NGN',
  SA: 'SAR',
  TZ: 'TZS',
  UG: 'UGX',
  US: 'USD',
  ZA: 'ZAR',
}

// KES is the catalogue base currency. These fallback rates keep the storefront usable offline.
const kesToCurrency: Record<string, number> = {
  AED: 0.028,
  AUD: 0.011,
  CAD: 0.011,
  CNY: 0.055,
  EUR: 0.0071,
  GBP: 0.0061,
  GHS: 0.12,
  INR: 0.65,
  JPY: 1.12,
  KES: 1,
  NGN: 12,
  SAR: 0.029,
  TZS: 18.3,
  UGX: 9.8,
  USD: 0.0077,
  ZAR: 0.14,
}

function getRegion(locale: string): string | null {
  const parts = locale.replace('_', '-').split('-')
  const region = parts.find((part) => /^[A-Z]{2}$/i.test(part) && part.toUpperCase() !== parts[0].toUpperCase())
  return region?.toUpperCase() ?? null
}

function detectCurrency(): string {
  const locales = navigator.languages?.length ? navigator.languages : [navigator.language]

  for (const locale of locales) {
    const region = getRegion(locale)
    if (region && currencyByRegion[region]) return currencyByRegion[region]
  }

  return 'KES'
}

const visitorCurrency = typeof navigator === 'undefined' ? 'KES' : detectCurrency()

export function formatProductPrice(priceInKes: string | number): string {
  const amountInKes = Number(priceInKes)
  const rate = kesToCurrency[visitorCurrency] ?? 1
  const amount = Number.isFinite(amountInKes) ? amountInKes * rate : 0
  const locale = typeof navigator === 'undefined' ? 'en-KE' : navigator.language || 'en-KE'

  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: visitorCurrency,
    maximumFractionDigits: visitorCurrency === 'JPY' || visitorCurrency === 'UGX' ? 0 : 2,
  }).format(amount)
}

export function getVisitorCurrency(): string {
  return visitorCurrency
}

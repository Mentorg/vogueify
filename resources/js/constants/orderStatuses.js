export const ORDER_STATUS_STEPS = [
  'pending',
  'paid',
  'confirmed',
  'processing',
  'shipped',
  'in-transit',
  'out-for-delivery',
  'delivered'
];

export const ORDER_EXCEPTION_STATUS_MAP  = {
  'attempted-delivery': 'Attempted Delivery',
  'awaiting-pickup': 'Awaiting Pickup',
  'delayed': 'Delayed',
  'held-at-customs': 'Held at Customs',
  'canceled': 'Canceled'
};

export const ACTIVE_ORDER_STATUSES = [
  'pending',
  'paid',
  'confirmed',
  'processing',
  'shipped',
  'in-transit',
  'out-for-delivery',
  'attempted-delivery',
  'awaiting-pickup',
  'delayed',
  'held-at-customs',
  'lost'
];

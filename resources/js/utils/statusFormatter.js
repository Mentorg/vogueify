import { capitalize } from "./capitalize";

export const formatStatus = (status) => {
  return status
    .split('-')
    .map(word => capitalize(word))
    .join(' ');
}

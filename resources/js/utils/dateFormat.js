export const formatDate = (dateString, separator, includeTime = false) => {
  let day, month, year, time = "";

  if (dateString.includes(" ")) {
    [dateString, time] = dateString.split(" ");
  } else if (dateString.includes("T")) {
    [dateString, time] = dateString.split("T");
    time = time?.split(".")[0] || "";
  }

  if (dateString.includes(".")) {
    const [dayPart, monthPart, yearPart] = dateString.split(separator);
    day = dayPart.padStart(2, "0");
    month = monthPart.padStart(2, "0");
    year = yearPart;
  }
  else if (dateString.includes("-")) {
    const [yearPart, monthPart, dayPart] = dateString.split("-");
    day = dayPart.padStart(2, "0");
    month = monthPart.padStart(2, "0");
    year = yearPart;
  } else {
    return "";
  }

  return includeTime && time
    ? `${day}${separator}${month}${separator}${year} ${time}`
    : `${day}${separator}${month}${separator}${year}`;
};

export function getCurrentDate() {
  const now = new Date();

  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const day = String(now.getDate()).padStart(2, '0');

  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

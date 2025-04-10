export const formatDate = (dateString, separator) => {
  let day, month, year;

  if (dateString.includes("T")) {
    dateString = dateString.split("T")[0];
  }

  if (dateString.includes(".")) {
    const [dayPart, monthPart, yearPart] = dateString.split(separator);
    day = dayPart.padStart(2, "0");
    month = monthPart.padStart(2, "0");
    year = yearPart;
  } else if (dateString.includes("-")) {
    const [yearPart, monthPart, dayPart] = dateString.split("-");
    day = dayPart.padStart(2, "0");
    month = monthPart.padStart(2, "0");
    year = yearPart;
  } else {
    return "";
  }

  return `${day}${separator}${month}${separator}${year}`;
};

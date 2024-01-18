export default {
    methods: {
        numberToArabicWords(number) {
            const units = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة', 'عشرة'];
            const tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
            const hundreds = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

            if (number === 0) {
                return 'صفر';
            }

            let words = '';

            // For hundreds place
            if (number >= 100) {
                const hundredsDigit = Math.floor(number / 100);
                words += hundreds[hundredsDigit] + ' ';
                number %= 100;
            }

            // For tens and units place
            if (number >= 10 && number < 20) {
                words += units[number % 10] + ' عشرة ';
            } else if (number >= 20) {
                const tensDigit = Math.floor(number / 10);
                words += units[number % 10] + ' و' + tens[tensDigit];
                words += ' ';
                number %= 10;
            }

            // Handling units place
            // if (number > 0 && number < 10) {
            //     words += units[number] + ' ';
            // }

            return words.trim();
        },


        decimalToArabicWords(decimal) {
            const units = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
            const tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
            const hundreds = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

            if (decimal === 0) {
                return '';
            }

            let words = '';

            // fractional part
            const decimalAsString = decimal.toString();
            const [_, decimalPart] = decimalAsString.split('.');

            if (decimalPart) {
                let fractionalPart = parseInt(decimalPart);

                // units and tens for the decimal part
                if (fractionalPart >= 100) {
                    const hundredsDigit = Math.floor(fractionalPart / 100);
                    words += ' و ' + hundreds[hundredsDigit];
                    fractionalPart %= 100;
                }

                if (fractionalPart >= 10) {
                    const tensDigit = Math.floor(fractionalPart / 10);
                    words += ' و ' + tens[tensDigit];
                    fractionalPart %= 10;
                }

                if (fractionalPart > 0) {
                    words += ' و ' + units[fractionalPart];
                }
            }
            console.log("word", words.trim())
            return words.trim();
        },

        convertToArabicWords(inputNumber) {
            const [integerPart, decimalPart] = inputNumber.toString().split('.');

            const integerWords = this.numberToArabicWords(parseInt(integerPart));
            const decimalWords = decimalPart ? this.decimalToArabicWords(parseFloat(`0.${decimalPart}`) * 100) : '';

            let result = integerWords;

            if (decimalWords) {

                result += ' و ' + decimalWords + ' فلس';
            }
            console.log("decimalWords", decimalWords)
            return result;
        },
    },
};

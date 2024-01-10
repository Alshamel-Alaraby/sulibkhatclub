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

            // Handling hundreds place
            if (number >= 100) {
                const hundredsDigit = Math.floor(number / 100);
                words += hundreds[hundredsDigit] + ' ';
                number %= 100;
            }

            // Handling tens and units place
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

            // Handling hundreds place for the decimal part
            if (decimal >= 100) {
                const hundredsDigit = Math.floor(decimal / 100);
                words += hundreds[hundredsDigit] + ' ';
                decimal %= 100;
            }

            // Handling tens and units place for the decimal part
            if (decimal >= 10) {
                if (decimal < 20) {
                    words += units[decimal % 10] + ' عشرة ';
                } else {
                    const tensDigit = Math.floor(decimal / 10);
                    words += tens[tensDigit] + ' ';
                    decimal %= 10;
                }
            }

            // Handling units place for the decimal part
            if (decimal > 0 && decimal < 10) {
                words += units[decimal] + ' ';
            }

            return words.trim();
        },

        convertToArabicWords(inputNumber) {
            const [integerPart, decimalPart] = inputNumber.toString().split('.');

            const integerWords = this.numberToArabicWords(parseInt(integerPart));
            const decimalWords = decimalPart ? this.decimalToArabicWords(parseFloat(`0.${decimalPart}`) * 100) : '';

            let result = integerWords;

            if (decimalWords) {
                // Combining the integer and decimal words with appropriate separators
                result += ' و ' + decimalWords + ' فلس';
            }

            return result;
        },
    },
};

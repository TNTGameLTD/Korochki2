class Validator {
    static patterns = {
        login: /^[a-zA-Z0-9]{5,}$/,
        password: /^.{6,}$/,
        fullName: /^[а-яА-ЯёЁ\s-]+$/,
        phone: /^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    };

    static validateLogin(login) {
        if (!login) return 'Логин обязателен';
        if (!this.patterns.login.test(login)) {
            return 'Логин должен содержать только латиницу и цифры, минимум 6 символов';
        }
        return '';
    }

    static validatePassword(password) {
        if (!password) return 'Пароль обязателен';
        if (!this.patterns.password.test(password)) {
            return 'Пароль должен содержать минимум 8 символов';
        }
        return '';
    }

    static validateFullName(name) {
        if (!name) return 'ФИО обязательно';
        if (!this.patterns.fullName.test(name)) {
            return 'ФИО должно содержать только кириллицу и пробелы';
        }
        return '';
    }

    static validatePhone(phone) {
        if (!phone) return 'Телефон обязателен';
        if (!this.patterns.phone.test(phone)) {
            return 'Телефон должен быть в формате 8(XXX)XXX-XX-XX';
        }
        return '';
    }

    static validateEmail(email) {
        if (!email) return 'Email обязателен';
        if (!this.patterns.email.test(email)) {
            return 'Введите корректный email';
        }
        return '';
    }

    static formatPhone(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value[0] === '7') value = '8' + value.slice(1);
            if (value[0] !== '8') value = '8' + value;
        }
        
        let formatted = '';
        if (value.length > 0) formatted += value[0];
        if (value.length > 1) formatted += '(' + value.slice(1, 4);
        if (value.length > 4) formatted += ')' + value.slice(4, 7);
        if (value.length > 7) formatted += '-' + value.slice(7, 9);
        if (value.length > 9) formatted += '-' + value.slice(9, 11);
        
        input.value = formatted;
    }
}
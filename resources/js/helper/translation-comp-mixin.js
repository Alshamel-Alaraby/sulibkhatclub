export default {
  methods: {
    getCompanyKey(key) {
      let returnedKey = null;
      for (let _key in this.companyKeys) {
        if (_key == key) {
          returnedKey =
            this.$i18n.locale == "ar"
              ? this.companyKeys[_key].new_ar
              : this.companyKeys[_key].new_en;
          return returnedKey;
        }
      }
      for (let _key in this.defaultsKeys) {
        if (_key == key) {
          returnedKey =
            this.$i18n.locale == "ar"
              ? this.defaultsKeys[_key].default_ar
              : this.defaultsKeys[_key].default_en;
          return returnedKey;
        }
      }
    },
    getKeyInfo(key) {
      let keyInfo = null;
      for (let _key in this.companyKeys) {
        if (_key == key) {
          keyInfo = this.companyKeys[_key];
          return keyInfo;
        }
      }
      return keyInfo;
    },
    getCompanyKeyLang(key, lang) {
      let returnedKey = null;
      for (let _key in this.companyKeys) {
        if (_key == key) {
          returnedKey =
            lang == "ar"
              ? this.companyKeys[_key].new_ar
              : this.companyKeys[_key].new_en;
          return returnedKey;
        }
      }
      for (let _key in this.defaultsKeys) {
        if (_key == key) {
          returnedKey =
            lang == "ar"
              ? this.defaultsKeys[_key].default_ar
              : this.defaultsKeys[_key].default_en;
          return returnedKey;
        }
      }
    },
  },
}

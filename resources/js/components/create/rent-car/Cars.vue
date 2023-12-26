<template>
    <div>
        <Country
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'country-create-car'"
            @created="getCountry"
        />
        <Transmission
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'transmission-create-car'"
            @created="getTransmission"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <Sunroof
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'sunroof-create-car'"
            @created="getSunroof"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <BodyType
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'body-type-create-car'"
            @created="bodyType"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <Color
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'color-interior-create-car'"
            @created="getColor"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <seatMaterial
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'seats-material-create-car'"
            @created="seatsMaterial"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <Specification
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'specifications-create-car'"
            @created="specification"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <FuelType
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :id="'fuel-type-create-car'"
            @created="fuelType"
            :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('car_car_create_form'):getCompanyKey('car_car_edit_form')"
            title-class="font-18"
            body-class="p-4 "
            dialog-class="modal-full-width"
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="card">
                    <div class="card-body">
                        <div class="my-1 d-flex justify-content-end">
                            <b-button
                                v-if="type != 'edit'"
                                variant="success"
                                :disabled="!is_disabled"
                                @click.prevent="resetForm"
                                type="button"
                                :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
                            >
                                {{ $t("general.AddNewRecord") }}
                            </b-button>
                            <template v-if="!is_disabled">
                                <b-button
                                    variant="success"
                                    type="button"
                                    class="mx-1"
                                    v-if="!isLoader"
                                    @click.prevent="AddSubmit"
                                >
                                    {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                                </b-button>

                                <b-button variant="success" class="mx-1" disabled v-else>
                                    <b-spinner small></b-spinner>
                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>
                            </template>
                            <!-- Emulate built in modal footer ok and cancel button actions -->

                            <b-button
                                variant="danger"
                                type="button"
                                @click.prevent="resetModalHidden"
                            >
                                {{ $t("general.Cancel") }}
                            </b-button>
                        </div>
                    </div>
                    <b-tabs nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active>
                            <div class="row">
                                <div class="col-md-3" v-if="isVisible('brand_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_brand") }}
                                            <span v-if="isRequired('brand_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showBrandModal"
                                            v-model="create.brand_id"
                                            :options="brands.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => brands.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? brands.find((x) => x.id == opt).name:
                                                brands.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.brand_id.$error || errors.brand_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.brand_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.brand_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('model_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_model") }}
                                            <span v-if="isRequired('model_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="create.model_id"
                                            :options="models.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => models.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? models.find((x) => x.id == opt).name:
                                                models.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.model_id.$error || errors.model_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.model_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.model_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('country_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_country") }}
                                            <span v-if="isRequired('country_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showCountryModal"
                                            v-model="create.country_id"
                                            :options="countries.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => countries.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? countries.find((x) => x.id == opt).name:
                                                countries.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.country_id.$error || errors.country_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.country_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.country_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('plate_number')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey('car_car_plate_number') }}
                                            <span v-if="isRequired('plate_number')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.plate_number.$model"
                                            :class="{
                                                'is-invalid': $v.create.plate_number.$error || errors.plate_number,
                                                'is-valid': !$v.create.plate_number.$invalid && !errors.plate_number,
                                              }"
                                        />
                                        <div v-if="!$v.create.plate_number.minLength" class="invalid-feedback">
                                            {{ $t("general.Itmustbeatleast") }}
                                            {{ $v.create.plate_number.$params.minLength.min }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <div v-if="!$v.create.plate_number.maxLength" class="invalid-feedback">
                                            {{ $t("general.Itmustbeatmost") }}
                                            {{ $v.create.plate_number.$params.maxLength.max }}
                                            {{ $t("general.letters") }}
                                        </div>
                                        <template v-if="errors.plate_number">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.plate_number"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('year_manufacture')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("financial_year_year") }}
                                            <span v-if="isRequired('year_manufacture')" class="text-danger">*</span>
                                        </label>
                                        <date-picker
                                            type="year"
                                            v-model="create.year_manufacture"
                                            format="YYYY"
                                            valueType="format"
                                            :confirm="false"
                                            ></date-picker>
                                        <div
                                            v-if="$v.create.year_manufacture.$error || errors.year_manufacture"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.year_manufacture">
                                            <ErrorMessage v-for="(errorMessage,index) in errors.year_manufacture"
                                                          :key="index">
                                                {{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('chassis_number')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey('car_car_chassis_number') }}
                                            <span v-if="isRequired('chassis_number')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.chassis_number.$model"
                                            :class="{
                                                'is-invalid': $v.create.chassis_number.$error || errors.chassis_number,
                                                'is-valid': !$v.create.chassis_number.$invalid && !errors.chassis_number,
                                              }"
                                        />
                                        <template v-if="errors.chassis_number">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.chassis_number"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <hr
                                style="
                                    margin: 10px 0 !important;
                                    border-top: 1px solid rgb(141 163 159 / 42%);
                                  "
                            />
                            <div class="row">
                                <div class="col-md-3" v-if="isVisible('transmission_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_transmission") }}
                                            <span v-if="isRequired('transmission_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showTransmissionModal"
                                            v-model="create.transmission_id"
                                            :options="transmissions.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => transmissions.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? transmissions.find((x) => x.id == opt).name:
                                                transmissions.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.transmission_id.$error || errors.transmission_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.transmission_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.transmission_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('sunroof_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_sunroof") }}
                                            <span v-if="isRequired('sunroof_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showSunroofModal"
                                            v-model="create.sunroof_id"
                                            :options="sunroofs.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => sunroofs.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? sunroofs.find((x) => x.id == opt).name:
                                                sunroofs.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.sunroof_id.$error || errors.sunroof_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.sunroof_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.sunroof_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('body_type_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_body_type") }}
                                            <span v-if="isRequired('body_type_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showBodyTypesModal"
                                            v-model="create.body_type_id"
                                            :options="body_types.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => body_types.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? body_types.find((x) => x.id == opt).name:
                                                    body_types.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.body_type_id.$error || errors.body_type_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.body_type_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.body_type_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('cylinders')" >
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">
                                            {{ getCompanyKey("car_car_cylinders") }}
                                            <span v-if="isRequired('cylinders')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            data-create="1"
                                            v-model="$v.create.cylinders.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.cylinders.$error ||
                                                errors.cylinders,
                                              'is-valid':
                                                !$v.create.cylinders.$invalid &&
                                                !errors.cylinders,
                                            }"
                                            id="field-1"
                                        />
                                        <template v-if="errors.cylinders">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.cylinders"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('color_exterior_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_color_exterior") }}
                                            <span v-if="isRequired('color_exterior_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showColorInteriorModal"
                                            v-model="create.color_exterior_id"
                                            :options="color_exteriors.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => color_exteriors.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? color_exteriors.find((x) => x.id == opt).name:
                                                    color_exteriors.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.color_exterior_id.$error || errors.color_exterior_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.color_exterior_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.color_exterior_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('fuel_type_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_fuel_type") }}
                                            <span v-if="isRequired('fuel_type_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showFuelTypeModal"
                                            v-model="create.fuel_type_id"
                                            :options="fuel_types.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => fuel_types.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? fuel_types.find((x) => x.id == opt).name:
                                                    fuel_types.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.fuel_type_id.$error || errors.fuel_type_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.fuel_type_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.fuel_type_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('color_interior_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_color_interior") }}
                                            <span v-if="isRequired('color_interior_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showColorInteriorModal"
                                            v-model="create.color_interior_id"
                                            :options="color_interiors.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => color_interiors.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? color_interiors.find((x) => x.id == opt).name:
                                                    color_interiors.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.color_interior_id.$error || errors.color_interior_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.color_interior_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.color_interior_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('seats_material_id')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_color_seats_material") }}
                                            <span v-if="isRequired('seats_material_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showSeatsMaterialModal"
                                            v-model="create.seats_material_id"
                                            :options="seats_materials.map((type) => type.id)"
                                            :custom-label="
                                              (opt) => seats_materials.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? seats_materials.find((x) => x.id == opt).name:
                                                    seats_materials.find((x) => x.id == opt).name_e:
                                                    null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.seats_material_id.$error || errors.seats_material_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.seats_material_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.seats_material_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('passengers')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_passengers") }}
                                            <span v-if="isRequired('passengers')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model.number="$v.create.passengers.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.passengers.$error ||
                                                errors.passengers,
                                              'is-valid':
                                                !$v.create.passengers.$invalid &&
                                                !errors.passengers,
                                            }"
                                        />
                                        <template v-if="errors.passengers">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.passengers"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('current_odometer')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_current_odometer") }}
                                            <span v-if="isRequired('current_odometer')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model.number="$v.create.current_odometer.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.current_odometer.$error ||
                                                errors.current_odometer,
                                              'is-valid':
                                                !$v.create.current_odometer.$invalid &&
                                                !errors.current_odometer,
                                            }"
                                        />
                                        <template v-if="errors.current_odometer">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.current_odometer"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('specifications')">
                                    <div class="form-group position-relative">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_specifications") }}
                                            <span v-if="isRequired('specifications')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            :multiple="true"
                                            @input="showSpecificationModal"
                                            v-model="create.specifications"
                                            :options="specifications.map((type) => type.id)"
                                            :custom-label="
                                          (opt) => specifications.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar'? specifications.find((x) => x.id == opt).name:
                                                specifications.find((x) => x.id == opt).name_e:
                                                null
                                          "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.specifications.$error || errors.specifications"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.specifications">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.specifications"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <hr
                                style="
                margin: 10px 0 !important;
                border-top: 1px solid rgb(141 163 159 / 42%);
              "
                            />
                            <div class="row">
                                <div class="col-md-3" v-if="isVisible('financial_value')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_financial_value") }}
                                            <span v-if="isRequired('financial_value')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            v-model.number="$v.create.financial_value.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.financial_value.$error ||
                                                errors.financial_value,
                                              'is-valid':
                                                !$v.create.financial_value.$invalid &&
                                                !errors.financial_value,
                                            }"
                                        />
                                        <template v-if="errors.financial_value">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.financial_value"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('depreciation_percentage')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_depreciation_percentage") }}
                                            <span v-if="isRequired('depreciation_percentage')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            v-model.number="$v.create.depreciation_percentage.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.depreciation_percentage.$error ||
                                                errors.depreciation_percentage,
                                              'is-valid':
                                                !$v.create.depreciation_percentage.$invalid &&
                                                !errors.depreciation_percentage,
                                            }"
                                        />
                                        <template v-if="errors.depreciation_percentage">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.depreciation_percentage"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('barcode')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_barcode") }}
                                            <span v-if="isRequired('barcode')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.barcode.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.barcode.$error ||
                                                errors.barcode,
                                              'is-valid':
                                                !$v.create.barcode.$invalid &&
                                                !errors.barcode,
                                            }"
                                        />
                                        <template v-if="errors.barcode">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.barcode"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('jpscode')" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_jpscode") }}
                                            <span v-if="isRequired('jpscode')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.jpscode.$model"
                                            :class="{
                                              'is-invalid':
                                                $v.create.jpscode.$error ||
                                                errors.jpscode,
                                              'is-valid':
                                                !$v.create.jpscode.$invalid &&
                                                !errors.jpscode,
                                            }"
                                        />
                                        <template v-if="errors.jpscode">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.jpscode"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <hr
                                style="
                                    margin: 10px 0 !important;
                                    border-top: 1px solid rgb(141 163 159 / 42%);
                                "
                                v-if="(isVisible('daily_price') || isVisible('weekly_price') || isVisible('monthly_price')) && type == 'edit'"
                            />
                            <div class="row">
                                <div class="col-md-3" v-if="isVisible('daily_price') && type == 'edit'" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_daily_price") }}
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            v-model.number="daily_price"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('weekly_price') && type == 'edit'" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_weekly_price") }}
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            v-model.number="weekly_price"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="isVisible('monthly_price') && type == 'edit'" >
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("car_car_monthly_price") }}
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            v-model.number="monthly_price"
                                        />
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab
                            :disabled="!car_id"
                            :title="$t('general.ImageUploads')"
                        >
                            <div class="row">
                                <input
                                    accept="image/png, image/gif, image/jpeg, image/jpg"
                                    type="file"
                                    id="uploadImageCreate"
                                    @change.prevent="onImageChanged"
                                    class="input-file-upload position-absolute"
                                    :class="['d-none']"
                                />
                                <div class="col-md-8 my-1">
                                    <!-- file upload -->
                                    <div
                                        class="row align-content-between"
                                        style="width: 100%; height: 100%"
                                    >
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap">
                                                <div
                                                    :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
                                                    v-for="(photo, index) in images"
                                                    :key="photo.id"
                                                >
                                                    <div
                                                        :class="[
                                      'card mb-0 shadow-none border',
                                      images.length - 1 == index ? 'bg-primary' : '',
                                    ]"
                                                    >
                                                        <div class="p-2">
                                                            <div class="row align-items-center">
                                                                <div
                                                                    class="col-auto"
                                                                    @click="showPhoto = photo.webp"
                                                                >
                                                                    <img
                                                                        data-dz-thumbnail
                                                                        :src="photo.webp"
                                                                        class="avatar-sm rounded bg-light"
                                                                        @error="src = img"
                                                                    />
                                                                </div>
                                                                <div class="col pl-0">
                                                                    <a
                                                                        href="javascript:void(0);"
                                                                        :class="[
                                              'font-weight-bold',
                                              images.length - 1 == index
                                                ? 'text-white'
                                                : 'text-muted',
                                            ]"
                                                                        data-dz-name
                                                                    >
                                                                        {{ photo.name }}
                                                                    </a>
                                                                </div>
                                                                <!-- Button -->
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    :class="[
                                            'btn-danger dropzone-close',
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                                                    data-dz-remove
                                                                    @click.prevent="
                                            deleteImageCreate(photo.id)
                                          "
                                                                >
                                                                    <i class="fe-x"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="footer-image col-12">
                                            <b-button
                                                @click="changePhoto"
                                                variant="success"
                                                type="button"
                                                class="mx-1 font-weight-bold px-3"
                                                v-if="!isLoader"
                                            >
                                                {{ $t("general.Add") }}
                                            </b-button>
                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                            </b-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="show-dropzone">
                                        <img
                                            :src="showPhoto"
                                            class="img-thumbnail"
                                            @error="src = img"
                                        />
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                    </b-tabs>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf,minValue,maxValue} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import img from "../../../assets/images/img-1.png";
import Swal from "sweetalert2";
import Country from "../general/country";
import Transmission from "./transmission";
import Sunroof from "./sun-roof";
import BodyType from "./body-type";
import Color from "../general/color";
import seatMaterial from "./seat-material";
import Specification from "./specification";
import FuelType from "./fuel-type";

export default {
    name: "group",
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        Country,
        Transmission,
        Sunroof,
        BodyType,
        Color,
        Specification,
        seatMaterial,
        FuelType
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/car-rent/car-cars'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            company_id:null,
            create: {
                plate_number: "", // 3
                brand_id: null, // 1
                model_id: null, // 2
                year_manufacture: null, // 4
                chassis_number: "", //5
                transmission_id: null, //6
                sunroof_id: null, //7
                body_type_id: null, // 8
                cylinders: 0, //10
                country_id: null, //9
                color_exterior_id: null, //11
                fuel_type_id: null, //12
                color_interior_id: null, //13
                seats_material_id: null,
                passengers: 0,//14
                financial_value: 0,//16
                current_odometer: 0, //15
                specifications: [],//17
                depreciation_percentage: 0,//18
                barcode: '',//19
                jpscode: ''//20
            },
            errors: {},
            is_disabled: false,
            brands: [],
            car_id: null,
            models: [],
            transmissions: [],
            sunroofs: [],
            body_types: [],
            countries: [],
            color_exteriors: [],
            fuel_types: [],
            color_interiors: [],
            seats_materials: [],
            specifications: [],
            idDelete: null,
            images: [],
            media: {},
            showPhoto: img,
            daily_price: 0,
            weekly_price: 0,
            monthly_price: 0
        };
    },
    validations: {
        create: {
            plate_number: { required: requiredIf(function (model) {
                    return this.isRequired("plate_number");
                }), minLength: minLength(1), maxLength: maxLength(50) },
            brand_id: {required: requiredIf(function (model) {
                    return this.isRequired("brand_id");
                })},
            model_id: {required: requiredIf(function (model) {
                    return this.isRequired("model_id");
                })},
            year_manufacture: {required: requiredIf(function (model) {
                    return this.isRequired("year_manufacture");
                })},
            chassis_number: {required: requiredIf(function (model) {
                    return this.isRequired("chassis_number");
                }), maxLength: maxLength(50)},
            transmission_id: {required: requiredIf(function (model) {
                    return this.isRequired("transmission_id");
                })},
            sunroof_id: {required: requiredIf(function (model) {
                    return this.isRequired("sunroof_id");
                })},
            body_type_id: {required: requiredIf(function (model) {
                    return this.isRequired("body_type_id");
                })},
            cylinders: {required: requiredIf(function (model) {
                    return this.isRequired("cylinders");
                }), minValue: minValue(0),maxValue: maxValue(99)},
            country_id: {required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                })},
            color_exterior_id: {required: requiredIf(function (model) {
                    return this.isRequired("color_exterior_id");
                })},
            fuel_type_id: {required: requiredIf(function (model) {
                    return this.isRequired("fuel_type_id");
                })},
            color_interior_id: {required: requiredIf(function (model) {
                    return this.isRequired("color_interior_id");
                })},
            seats_material_id: {required: requiredIf(function (model) {
                    return this.isRequired("seats_material_id");
                })},
            passengers: {required: requiredIf(function (model) {
                    return this.isRequired("passengers");
                }), minValue: minValue(0),maxValue: maxValue(9)},
            financial_value: {required: requiredIf(function (model) {
                    return this.isRequired("financial_value");
                }), minValue: minValue(0)},
            current_odometer: {required: requiredIf(function (model) {
                    return this.isRequired("current_odometer");
                }), minValue: minValue(0)},
            specifications: {required: requiredIf(function (model) {
                    return this.isRequired("specifications");
                })},
            depreciation_percentage: {required: requiredIf(function (model) {
                    return this.isRequired("depreciation_percentage");
                }), minValue: minValue(0), maxValue: maxValue(100)},
            barcode: { required: requiredIf(function (model) {
                    return this.isRequired("barcode");
                }), minLength: minLength(1), maxLength: maxLength(50) },
            jpscode: { required: requiredIf(function (model) {
                    return this.isRequired("jpscode");
                }), minLength: minLength(1), maxLength: maxLength(50) }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/car_cars`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        isVisible(fieldName) {
            if(!this.isPage){
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            }else {
                return this.isVisiblePage(fieldName);
            }
        },
        isRequired(fieldName) {
            if(!this.isPage) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            }else {
                return this.isRequiredPage(fieldName);
            }
        },
        defaultData(edit = null){
            this.create = {
                plate_number: "",
                brand_id: null,
                model_id: null,
                year_manufacture: null,
                chassis_number: "",
                transmission_id: null,
                sunroof_id: null,
                body_type_id: null,
                cylinders: 0,
                country_id: null,
                color_exterior_id: null,
                fuel_type_id: null,
                color_interior_id: null,
                seats_material_id: null,
                passengers: 0,
                financial_value: 0,
                current_odometer: 0,
                specifications: [],
                depreciation_percentage: 0,
                barcode: '',
                jpscode: ''
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.car_id = null;
            this.media = {};
            this.showPhoto = img;
            this.images = [];
            this.is_disabled = false;
            this.daily_price = 0;
            this.weekly_price = 0;
            this.monthly_price = 0;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('color_interior_id') && this.isVisible('color_exterior_id')) this.getColor();
                    if(this.isVisible('brand_id')) this.getBrand();
                    if(this.isVisible('transmission_id')) this.getTransmission();
                    if(this.isVisible('sunroof_id')) this.getSunroof();
                    if(this.isVisible('body_type_id')) this.bodyType();
                    if(this.isVisible('country_id')) this.getCountry();
                    if(this.isVisible('fuel_type_id')) this.fuelType();
                    if(this.isVisible('seats_material_id')) this.seatsMaterial();
                    if(this.isVisible('specifications')) this.specification();
                }else {
                    if(this.idObjEdit.dataObj){
                        let car = this.idObjEdit.dataObj;
                        this.car_id = car.id;
                        this.create.errors = {};
                        this.create.plate_number = car.plate_number;
                        this.create.brand_id = car.brand_id;
                        this.create.year_manufacture= car.year_manufacture;
                        this.create.chassis_number= car.chassis_number;
                        this.create.transmission_id= car.transmission_id;
                        this.create.sunroof_id= car.sunroof_id;
                        this.create.body_type_id= car.body_type_id;
                        this.create.cylinders= car.cylinders;
                        this.create.country_id= car.country_id;
                        this.create.color_exterior_id= car.color_exterior_id;
                        this.create.fuel_type_id= car.fuel_type_id;
                        this.create.color_interior_id= car.color_interior_id;
                        this.create.seats_material_id= car.seats_material_id;
                        this.create.passengers= car.passengers;
                        this.create.financial_value= car.financial_value;
                        this.create.current_odometer= car.current_odometer;
                        this.create.specifications= car.specifications;
                        this.create.depreciation_percentage= car.depreciation_percentage;
                        this.create.barcode= car.barcode;
                        this.create.jpscode= car.jpscode;
                        this.daily_price = car.daily_price;
                        this.weekly_price = car.weekly_price;
                        this.monthly_price = car.monthly_price;
                        this.images = car.media ?? [];
                        if (this.images && this.images.length > 0) {
                            this.showPhoto = this.images[this.images.length - 1].webp;
                        } else {
                            this.showPhoto = img;
                        }
                        if(this.isVisible('brand_id')) await this.getBrand();
                        this.models =  this.brands.find(el => el.id == car.brand_id).children;
                        this.create.model_id= car.model_id;
                        if(this.isVisible('color_interior_id') && this.isVisible('color_exterior_id')) this.getColor();
                        if(this.isVisible('transmission_id')) this.getTransmission();
                        if(this.isVisible('sunroof_id')) this.getSunroof();
                        if(this.isVisible('body_type_id')) this.bodyType();
                        if(this.isVisible('country_id')) this.getCountry();
                        if(this.isVisible('fuel_type_id')) this.fuelType();
                        if(this.isVisible('seats_material_id')) this.seatsMaterial();
                        if(this.isVisible('specifications')) this.specification();
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {...this.create,company_id:this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            this.car_id = res.data.data.id;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");

                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }
            }
        },
        showBrandModal() {
            if (this.create.brand_id > 0) {
                this.create.model_id = null;
                this.models = this.brands.find( el => el.id == this.create.brand_id).children;
            }else {
                this.create.model_id = null;
                this.models = [];
            }
        },
        showCountryModal() {
            if (this.create.country_id == 0) {
                this.$bvModal.show("country-create-car");
                this.create.country_id = null;
            }
        },
        showTransmissionModal(){
            if (this.create.transmission_id == 0) {
                this.$bvModal.show("transmission-create-car");
                this.create.transmission_id = null;
            }
        },
        showSunroofModal(){
            if (this.create.sunroof_id == 0) {
                this.$bvModal.show("sunroof-create-car");
                this.create.sunroof_id = null;
            }
        },
        showBodyTypesModal(){
            if (this.create.body_type_id == 0) {
                this.$bvModal.show("body-type-create-car");
                this.create.body_type_id = null;
            }
        },
        showFuelTypeModal(){
            if (this.create.fuel_type_id == 0) {
                this.$bvModal.show("fuel-type-create-car");
                this.create.fuel_type_id = null;
            }
        },
        showColorInteriorModal(){
            if (this.create.color_interior_id == 0 || this.create.color_exterior_id == 0) {
                this.$bvModal.show("color-interior-create-car");
                if(this.create.color_interior_id == 0) this.create.color_interior_id = null;
                if(this.create.color_exterior_id == 0) this.create.color_exterior_id = null;
            }
        },
        showSeatsMaterialModal(){
            if (this.create.seats_material_id == 0) {
                this.$bvModal.show("seats-material-create-car");
                this.create.seats_material_id = null;
            }
        },
        showSpecificationModal(){
            if (this.create.specifications == 0) {
                this.$bvModal.show("specifications-create-car");
                this.create.specifications = null;
            }
        },
        changePhoto() {
            document.getElementById("uploadImageCreate").click();
        },
        changePhotoEdit() {
            document.getElementById("uploadImageEdit").click();
        },
        onImageChanged(e) {
            const file = e.target.files[0];
            this.addImage(file);
        },
        addImage(file) {
            this.media = file; //upload
            if (file) {
                this.idDelete = null;
                let is_media = this.images.find(
                    (e) => e.name == file.name.slice(0, file.name.indexOf("."))
                );
                this.idDelete = is_media ? is_media.id : null;
                if (!this.idDelete) {
                    this.isLoader = true;
                    let formDate = new FormData();
                    formDate.append("media[0]", this.media);
                    adminApi
                        .post(`/media`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .put(`/car-rent/car-cars/${this.car_id}`, {
                                    old_media,
                                    media: new_media,
                                })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = img;
                                    }
                                })
                                .catch((err) => {
                                    this.errorFun('Error','Thereisanerrorinthesystem');
                                });
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                } else {
                    Swal.fire({
                        title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: `${this.$t("general.Replace")}`,
                        cancelButtonText: `${this.$t("general.Nocancel")}`,
                        confirmButtonClass: "btn btn-success mt-2",
                        cancelButtonClass: "btn btn-danger ml-2 mt-2",
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.value) {
                            this.isLoader = true;
                            let formDate = new FormData();
                            formDate.append("media[0]", this.media);
                            adminApi
                                .post(`/media`, formDate)
                                .then((res) => {
                                    let old_media = [];
                                    this.images.forEach((e) => old_media.push(e.id));
                                    old_media.splice(old_media.indexOf(this.idDelete), 1);
                                    let new_media = [];
                                    res.data.data.forEach((e) => new_media.push(e.id));

                                    adminApi
                                        .put(`/car-rent/car-cars/${this.car_id}`, {
                                            old_media,
                                            media: new_media,
                                        })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto = this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = img;
                                            }
                                        })
                                        .catch((err) => {
                                            this.errorFun('Error','Thereisanerrorinthesystem');
                                        });
                                })
                                .catch((err) => {
                                    if (err.response.data) {
                                        this.errors = err.response.data.errors;
                                    } else {
                                        this.errorFun('Error','Thereisanerrorinthesystem');
                                    }
                                })
                                .finally(() => {
                                    this.isLoader = false;
                                });
                        }
                    });
                }
            }
        },
        deleteImageCreate(id) {
            let old_media = [];
            this.images.forEach((e) => {
                if (e.id != id) {
                    old_media.push(e.id);
                }
            });
            adminApi
                .put(`/car-rent/car-cars/${this.car_id}`, { old_media })
                .then((res) => {
                    if(this.isPage){
                        let index = this.tables.findIndex(el => el.id == this.idObjEdit.idEdit);
                        this.tables[index] = res.data.data;
                    }
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = img;
                    }
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        getColor() {
            this.isLoader = true;

            adminApi
                .get(`/colors`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Color")) {
                        l.unshift({ id: 0, name: "اضف لون", name_e: "Add Color" });
                    }
                    this.color_exteriors = l;
                    this.color_interiors = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        async getBrand() {
            this.isLoader = true;

            await adminApi
                .get(`/brands?isModel=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.brands = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getTransmission() {
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-transmissions`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Transmission")) {
                        l.unshift({ id: 0, name: "اضف ناقل الحركه", name_e: "Add Transmission" });
                    }
                    this.transmissions = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getSunroof(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-sunroofs`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Sunroof")) {
                        l.unshift({ id: 0, name: "اضف فتحه السقف", name_e: "Add Sunroof" });
                    }
                    this.sunroofs = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        bodyType(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-body-types`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Body Type")) {
                        l.unshift({ id: 0, name: "اضف نوع الهيكل", name_e: "Add Body Types" });
                    }
                    this.body_types = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getCountry(){
            this.isLoader = true;

            adminApi
                .get(`/countries/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create Country")) {
                        l.unshift({ id: 0, name: "اضف بلد الصنع", name_e: "Add Country" });
                    }
                    this.countries = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        fuelType(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-fuel-types`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create FuelType")) {
                        l.unshift({ id: 0, name: "اضف نوع الوقود", name_e: "Add Fuel Type" });
                    }
                    this.fuel_types = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        seatsMaterial(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-seats-material`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create FuelType")) {
                        l.unshift({ id: 0, name: "اضف نوع المقاعد", name_e: "Add seats materials" });
                    }
                    this.seats_materials = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        specification(){
            this.isLoader = true;

            adminApi
                .get(`/car-rent/car-specifications`)
                .then((res) => {
                    let l = res.data.data;
                    if (this.isPermission("create specifications")) {
                        l.unshift({ id: 0, name: "اضف المواصفات", name_e: "Add Specifications" });
                    }
                    this.specifications = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>

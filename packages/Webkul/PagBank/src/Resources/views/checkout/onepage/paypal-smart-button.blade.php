@if (
    request()->routeIs('shop.checkout.onepage.index')
    && (bool) core()->getConfigData('sales.payment_methods.pagbank_smart_button.active')
)
    @php
        $clientId = core()->getConfigData('sales.payment_methods.pagbank_smart_button.client_id');

        $acceptedCurrency = core()->getConfigData('sales.payment_methods.pagbank_smart_button.accepted_currencies');
    @endphp

    @pushOnce('scripts')
        <script
            src="https://www.pagbank.com/sdk/js?client-id={{ $clientId }}&currency={{ $acceptedCurrency }}"
            data-partner-attribution-id="Bagisto_Cart"
        >
        </script>

        <script
            type="text/x-template"
            id="v-pagbank-smart-button-template"
        >
            <div class="w-full pagbank-button-container"></div>
        </script>

        <script type="module">
            app.component('v-pagbank-smart-button', {
                template: '#v-pagbank-smart-button-template',

                mounted() {
                    this.register();
                },

                methods: {
                    register() {
                        if (typeof pagbank == 'undefined') {
                            this.$emitter.emit('add-flash', { type: 'error', message: '@lang('Something went wrong.')' });

                            return;
                        }

                        pagbank.Buttons(this.getOptions()).render('.pagbank-button-container');
                    },

                    getOptions() {
                        let options = {
                            style: {
                                layout: 'vertical',
                                shape: 'rect',
                            },

                            authorizationFailed: false,

                            enableStandardCardFields: false,

                            alertBox: (message) => {
                                this.$emitter.emit('add-flash', { type: 'error', message: message });
                            },

                            createOrder: (data, actions) => {
                                return this.$axios.get("{{ route('pagbank.smart-button.create-order') }}")
                                    .then(response => response.data.result)
                                    .then(order => order.id)
                                    .catch(error => {
                                        if (error.response.data.error === 'invalid_client') {
                                            options.authorizationFailed = true;

                                            options.alertBox('@lang('Something went wrong.')');
                                        }

                                        return error;
                                    });
                            },

                            onApprove: (data, actions) => {
                                this.$axios.post("{{ route('pagbank.smart-button.capture-order') }}", {
                                    _token: "{{ csrf_token() }}",
                                    orderData: data
                                })
                                .then(response => {
                                    if (response.data.success) {
                                        if (response.data.redirect_url) {
                                            window.location.href = response.data.redirect_url;
                                        } else {
                                            window.location.href = "{{ route('shop.checkout.onepage.success') }}";
                                        }
                                    }
                                })
                                .catch(error => window.location.href = "{{ route('shop.checkout.cart.index') }}");
                            },

                            onError: (error) => {
                                if (! options.authorizationFailed) {
                                    options.alertBox('@lang('Something went wrong.')');
                                }
                            },
                        };

                        return options;
                    },
                },
            });
        </script>
    @endPushOnce
@endif

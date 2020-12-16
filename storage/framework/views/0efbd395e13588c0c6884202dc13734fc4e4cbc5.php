<?php $__env->startPush('top'); ?>
<div class="navbar is-dark">
    <div class="navbar-menu">
        <div class="navbar-start"></div>
        <div class="navbar-end">
            <div class="navbar-item">
                <a href="/logout" class="button is-danger">
                    <i class="fa fa-power-off"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('body'); ?>
<div class="container is-fluid">
    <div class="columns is-mobile is-multiline m-0">

        <div class="column is-3-desktop is-4-tablet is-full-mobile m-0 p-0 p-fixed">
            <div class="columns is-mobile is-multiline m-0 p-0">
                
                <div class="column is-full pb-0">
                    <div id="balances" class="box p-y-1 p-x-3">
                        <div v-if="isLoading" class="has-text-centered">
                            <i class="fa fa-circle-notch fa-spin"></i>
                        </div>
                        <div v-else v-for="client of clients" class="level mb-0">
                            <div class="level-left">
                                <div class="level-item c-default">
                                    <span class="subtitle">{{client.user.username.ucFirst()}}</span>
                                </div>
                            </div>
                            <div class="level-right">
                                <div class="level-item d-block">
                                    <div class="text-size-3">{{ BTCEUR(client) }} EUR</div>
                                    <div class="text-size-1">{{ parseFloat(client.wallet.total.balance).toFixed(8) }} BTC</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="column is-full pb-0">
                    <div class="box pt-2" id="exchange">
                        <div class="level">
                            <div class="level-left">
                                <div class="level-item">
                                    <div class="title m-0 p-0 mb-2 b-0 bb-fade-1"> Tasas </div>
                                </div>
                            </div>
                            <div class="level-right">
                                <div class="level-item">
                                    <div class="title m-0 p-0 mb-2 b-0 bb-fade-1 has-text-grey">
                                        {{ percent }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="isLoading" class="has-text-centered">
                            <i class="fa fa-circle-notch fa-spin"></i>
                        </div>
                        <div v-else class="level" v-for="(item, key) in all">
                            <div class="level-left">
                                <div class="level-item">
                                    <strong>{{key.ucFirst()}}</strong>
                                </div>
                            </div>
                            <div class="level-ritgth">
                                <div class="level-item">
                                    {{ numeral(item.price).format('0,0.00')+'&nbsp;' }}<strong>
                                        {{item.currency}}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="column is-9-desktop is-full-mobile is-offset-3 p-0">
            
            <div class="column is-full pb-0">
                <div class="box" id="filterForm">
                    <div class="field has-addons">
                        <div class="control is-expanded">
                            <button class="button w-100" @click="action='buy'"
                                :class="action=='buy'?'is-success':''">COMPRAR</button>
                        </div>
                        <div class="control is-expanded">
                            <button class="button w-100" @click="action='sell'"
                                :class="action=='sell'?'is-danger':''">VENDER</button>
                        </div>
                        <div class="control is-expanded">
                            <div class="select w-100">
                                <select class="w-100" v-model="countrie">
                                    <option v-for="item of countries" :value="item.countrie">{{item.name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="control">
                            <div class="select">
                                <select id="currency" v-model="currency">
                                    <option v-for="item of countries" :value="item.currency">{{item.currency}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control">
                            <button @click="search" class="button is-link">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="field has-addons">
                        <div class="control">
                            <input type="text" class="input" v-model="bankName" placeholder="Busqueda...">
                        </div>
                        <div class="control">
                            <input class="input has-text-right" has-number v-model="RealAmount"></input>
                        </div>
                        <div v-if="isLoading" class="control">
                            <div class="button is-static pr-0">
                                {{ message }}
                                <span class="fa-stack">
                                    <i class="fa-circle-notch fa-spin fa-stack-2x fas has-text-danger"></i>
                                    <i style="font-style: inherit;">{{page}}</i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div id="list-ads" class="columns m-0 is-mobile is-multiline is-vcentered">
                <div v-for="item of ads" class="column is-3-desktop is-full-mobile ads-item">
                    <div class="box pb-1" @click="open(item.link)">
                        <div class="media-content">
                            <div class="content">
                                <div class="title is-text-nowrap text-size-2 mb-2" v-html="item.bank_name||'&nbsp;'"></div>
                                <div class="has-text-right text-size-2">
                                    {{ numeral(item.price).format('0,0.0')+' '+item.currency }}
                                </div>
                                <div> <strong class="text-size-1" v-html="name(item)"></strong> </div>
                                <div class="level bt-fade-1 b-0">
                                    <div class="level-left"></div>
                                    <div class="level-rigth">
                                        <div class="level-item">
                                            {{ moment(item.last_seen*1000).fromNow() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>


    </div>

</div>
<?php $__env->stopPush(); ?>





<?php $__env->startPush('script'); ?>
<script type="text/javascript">
    $(function () {
        $('[has-number]').number();
    });
    const Store = Vue.$storage;


    const Balances = new Vue({
        el:'#balances',
        mounted(){ this.fresh(); },
        methods:{
            BTCEUR(client){
                return numeral(parseFloat(client.wallet.total.balance)*this.kraken).format('0,0.00');
            },
            fresh(){
                new AsyncXHR('/balances')
                .cache(true,1)
                .before(()=>(this.isLoading=true))
                .then(response=>{
                    this.clients=response.data,
                    this.isLoading=false,
                    setTimeout(this.fresh,90000);
                });
            },
        },
        data:{
            clients:[],
            kraken:0,
            isLoading:false,
        },
    });
    const Exchange = new Vue({
        el: '#exchange',
        mounted() {
            this.fresh();
        },
        methods: {
            numeral,
            fresh() {
                clearTimeout(this.timeOut);
                this.xhr.abort();
                this.xhr = new AsyncXHR('/exchange')
                    .cache(false)
                    .before(()=>(this.isLoading=true))
                    .allways(() => (this.timeOut = setTimeout(() => {
                        this.fresh();
                    }, 60000)))
                    .then((response) => {
                        this.all = response.data,
                        this.isLoading=false;
                        if(this.all.kraken){
                            Balances.kraken=this.all.kraken.price;
                        }
                    });
            },
        },
        watch:{
            all(value){ this.percent=(((this.all.vitaly.price/this.all.kraken.price)*100)-100).toFixed(1); },
        },
        data: {
            timeOut: 0,
            xhr: { abort() {} },
            all: {},
            isLoading:false,
            percent:0,
        },
    });
    const FilterForm = new Vue({
        el: '#filterForm',
        mounted() {
            new AsyncXHR('/json/currencies')
                .cache(true, 86400)
                .then((response) => {
                    this.countries = response.data;
                });
        },
        methods: {
            search() {
                return this.page = 1,
                    Announcements.fresh();
            },
        },
        computed: {
            action: Store.computed('action', 'sell'),
            currency: Store.computed('currency','VES'),
            amount() {
                return parseFloat(this.RealAmount.toString()
                    .replace(/\./gi, '')
                    .replace(/\,/gi, '.'));
            },
            countrie: Store.computed('countrie', 'VE', {
                set(iso) {
                    this.currency = this.countries.where('countrie', iso).first().currency;
                },
            }),
            bankName: Store.computed('bankName', '', {
                transform(value) {
                    return value.toLowerCase().replace(/[^a-zA-Z0-9,]/gi, '').split(',');
                },
            }),
        },
        data: {
            page: 1,
            RealAmount: 0,
            values: {},
            countries: [],
            isLoading:false,
            message:'CARGANDO',
            total:0,
        },
    });


    const Announcements = new Vue({
        el: '#list-ads',
        methods: {
            open: (...a) => window.open(...a),
            numeral,
            moment,
            name(item){
                return item.profile.name.replace(/^(\w+)(\s+)(.*)/gi,"$1 <br> $3");
            },
            fresh() {
                this.request.abort(),
                    this.request = new AsyncXHR('/ads')
                    .cache(false)
                    .error(()=>{
                        return FilterForm.message='REINTENTANDO',
                                this.fresh();
                    })
                    .input({
                        action: FilterForm.action,
                        currency: FilterForm.currency,
                        page: FilterForm.page,
                    })
                    .onabort(()=>(clearTimeout(this.timeOut),FilterForm.isLoading=false))
                    .before(()=>{
                        clearTimeout(this.timeOut),
                        FilterForm.message='CARGANDO',
                        FilterForm.isLoading=true;
                    })
                    .allways(()=>{FilterForm.total=this.list.length})
                    .then((ads) => {
                        let data = ads.data;
                        data.ads.forEach(item => {
                            this.$set(this.list, item.ad_id, item);
                        });
                        this.ads=Object.values(this.list).filter((item) => {
                            return (true
                                &&FilterForm.currency == item.currency
                                &&(FilterForm.amount>0?(
                                    FilterForm.amount<=item.max_amount
                                    &&FilterForm.amount >= item.min_amount
                                ):true)
                                &&(FilterForm.bankName.length?FilterForm.bankName.filter(key => {
                                    return (item.bank_filter.indexOf(key) >= 0);
                                }).length:true)
                                &&('ONLINE_' + FilterForm.action.toUpperCase())!=item.trade_type.toUpperCase()
                            );
                        }).sort((current, next) => {
                            let minPrice = (current.price < next.price);
                            return (FilterForm.action == 'buy')?(minPrice?-1:1):(minPrice?1:-1);
                        });
                        return (data.next) ? (FilterForm.page = data.next, this.fresh()) : (
                            this.timeOut=setTimeout(() => this.fresh(), (10*60*1000)),
                            FilterForm.isLoading = false,
                            FilterForm.page = 1
                        );
                    });
            },
        },
        data: {
            list: {},
            ads:[],
            request: {abort() {}},
            timeOut: setTimeout($.noop, 1),
        },
    });

</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('base.basic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /homepages/31/d750175303/htdocs/admin/resources/views/pages/dashboard.blade.php ENDPATH**/ ?>
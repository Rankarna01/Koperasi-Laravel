@extends('layouts.admin')

@section('title', 'Kasir POS - Penjualan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Kasir POS</span>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-140px)]">
    <!-- Kiri: Daftar Barang -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-800"><i class="fas fa-box text-primary-500 mr-2"></i> Produk</h3>
            
            <div class="relative w-64">
                <input type="text" id="searchBarang" placeholder="Cari nama / kode barang..." 
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition shadow-sm">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            </div>
        </div>

        <div class="p-4 flex-1 overflow-y-auto bg-slate-50/30">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="productList">
                @foreach($barangList as $b)
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:border-primary-300 hover:shadow-md transition cursor-pointer flex flex-col h-full product-item"
                        onclick="addToCart({{ $b->id }}, '{{ $b->nama }}', {{ $b->harga_jual }}, {{ $b->stok }})"
                        data-name="{{ strtolower($b->nama) }}" data-code="{{ strtolower($b->kode_barang) }}">
                        <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-lg flex items-center justify-center text-xl mb-3">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <p class="text-xs text-slate-400 mb-1 font-mono">{{ $b->kode_barang }}</p>
                        <h4 class="font-bold text-slate-800 text-sm leading-tight flex-1">{{ $b->nama }}</h4>
                        <div class="mt-3 flex items-end justify-between">
                            <span class="text-primary-600 font-bold text-sm">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</span>
                            <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded font-medium border border-slate-200">Stok: {{ $b->stok }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div id="noProductMessage" class="hidden text-center py-12">
                <i class="fas fa-search-minus text-4xl text-slate-300 mb-3"></i>
                <p class="text-slate-500 font-medium">Barang tidak ditemukan</p>
            </div>
        </div>
    </div>

    <!-- Kanan: Keranjang Kasir -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl border border-slate-100 shadow-sm h-full overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800"><i class="fas fa-shopping-cart text-primary-500 mr-2"></i> Keranjang</h3>
            <button onclick="clearCart()" class="text-xs font-medium text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded transition">Kosongkan</button>
        </div>

        <div class="flex-1 overflow-y-auto p-4" id="cartContainer">
            <div id="emptyCartMessage" class="h-full flex flex-col items-center justify-center text-slate-400">
                <i class="fas fa-cart-arrow-down text-4xl mb-3 text-slate-200"></i>
                <p class="text-sm font-medium">Keranjang masih kosong</p>
            </div>
            <div id="cartItems" class="space-y-3">
                <!-- Cart items will be appended here -->
            </div>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50">
            <div class="mb-3">
                <label class="text-xs font-semibold text-slate-600 uppercase">Anggota (Opsional)</label>
                <select id="anggota_id" class="w-full mt-1 border border-slate-200 rounded-xl py-2 px-3 text-sm focus:ring-primary-500 focus:border-primary-500 bg-white">
                    <option value="">-- Bukan Anggota (Umum) --</option>
                    @foreach(\App\Models\Anggota::aktif()->get() as $a)
                        <option value="{{ $a->id }}">{{ $a->no_anggota }} - {{ $a->nama_lengkap }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1"><i class="fas fa-info-circle"></i> Memilih anggota akan menambah SHU mereka.</p>
            </div>

            <div class="flex justify-between items-end mb-4 bg-white p-3 rounded-xl border border-primary-100 shadow-sm">
                <span class="text-slate-500 font-medium text-sm">Total Tagihan</span>
                <span class="text-2xl font-bold text-primary-600" id="cartTotalDisplay">Rp 0</span>
            </div>

            <button onclick="showPaymentModal()" id="btnBayar" disabled class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary-500/30 flex justify-center items-center gap-2">
                <i class="fas fa-cash-register"></i> BAYAR SEKARANG
            </button>
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div id="paymentModal" class="modal-backdrop hidden flex items-center justify-center">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden m-4">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="text-center mb-6">
                <p class="text-sm text-slate-500 font-medium">Total Tagihan</p>
                <p class="text-4xl font-bold text-slate-800 mt-1" id="modalTotalDisplay">Rp 0</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative flex flex-col items-center justify-center p-3 border-2 border-primary-500 rounded-xl cursor-pointer bg-primary-50" id="lblTunai">
                            <input type="radio" name="metode" value="tunai" checked class="hidden" onchange="changeMethod('tunai')">
                            <i class="fas fa-money-bill-wave text-primary-600 text-xl mb-1"></i>
                            <span class="text-xs font-bold text-primary-700">Tunai</span>
                        </label>
                        <label class="relative flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition" id="lblTransfer">
                            <input type="radio" name="metode" value="transfer" class="hidden" onchange="changeMethod('transfer')">
                            <i class="fas fa-building-columns text-slate-400 text-xl mb-1"></i>
                            <span class="text-xs font-medium text-slate-600">Transfer</span>
                        </label>
                        <label class="relative flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition" id="lblQris">
                            <input type="radio" name="metode" value="qris" class="hidden" onchange="changeMethod('qris')">
                            <i class="fas fa-qrcode text-slate-400 text-xl mb-1"></i>
                            <span class="text-xs font-medium text-slate-600">QRIS</span>
                        </label>
                    </div>
                </div>

                <div id="bayarContainer">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Uang Diterima (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-medium">Rp</span>
                        <input type="text" id="inputBayar" onkeyup="formatCurrency(this); calculateKembalian()" class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-lg font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <!-- Quick Cash Buttons -->
                    <div class="grid grid-cols-4 gap-2 mt-2">
                        <button onclick="setUangPas()" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-medium text-slate-700 transition border border-slate-200">Uang Pas</button>
                        <button onclick="addBayar(50000)" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-medium text-slate-700 transition border border-slate-200">50k</button>
                        <button onclick="addBayar(100000)" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-xs font-medium text-slate-700 transition border border-slate-200">100k</button>
                        <button onclick="clearBayar()" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg text-xs font-medium text-red-600 transition border border-red-100">Clear</button>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-slate-500 font-medium text-sm">Kembalian</span>
                    <span class="text-2xl font-bold text-green-600" id="kembalianDisplay">Rp 0</span>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50 flex gap-3">
            <button onclick="closePaymentModal()" class="flex-1 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-medium hover:bg-slate-50 transition">Batal</button>
            <button onclick="prosesPenjualan()" id="btnProses" class="flex-1 py-2.5 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition shadow-md shadow-primary-500/30">
                Proses Transaksi
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    let cartTotal = 0;

    // Search Barang
    $('#searchBarang').on('input', function() {
        const query = $(this).val().toLowerCase();
        let found = false;
        
        $('.product-item').each(function() {
            const name = $(this).data('name');
            const code = $(this).data('code');
            
            if(name.includes(query) || code.includes(query)) {
                $(this).show();
                found = true;
            } else {
                $(this).hide();
            }
        });
        
        if(!found) {
            $('#noProductMessage').removeClass('hidden');
        } else {
            $('#noProductMessage').addClass('hidden');
        }
    });

    function addToCart(id, name, price, maxStok) {
        // Cek apakah barang sudah ada di keranjang
        const index = cart.findIndex(item => item.id === id);
        
        if (index !== -1) {
            // Update qty jika masih ada stok
            if (cart[index].qty < maxStok) {
                cart[index].qty += 1;
            } else {
                showToast('warning', 'Stok maksimum tercapai!');
                return;
            }
        } else {
            // Add new
            if (maxStok < 1) {
                showToast('error', 'Stok habis!');
                return;
            }
            cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1,
                max: maxStok
            });
        }
        
        renderCart();
    }

    function renderCart() {
        const container = $('#cartItems');
        container.empty();
        cartTotal = 0;

        if (cart.length === 0) {
            $('#emptyCartMessage').removeClass('hidden');
            $('#btnBayar').prop('disabled', true);
            $('#cartTotalDisplay').text('Rp 0');
            return;
        }

        $('#emptyCartMessage').addClass('hidden');
        $('#btnBayar').prop('disabled', false);

        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            cartTotal += subtotal;

            container.append(`
                <div class="flex items-center justify-between p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                    <div class="flex-1 min-w-0 pr-3">
                        <h4 class="font-semibold text-slate-800 text-sm truncate">${item.name}</h4>
                        <p class="text-xs text-primary-600 font-bold mt-1">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50">
                            <button onclick="updateQty(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-slate-200 rounded-l-lg transition"><i class="fas fa-minus text-[10px]"></i></button>
                            <span class="w-8 text-center text-sm font-semibold text-slate-800">${item.qty}</span>
                            <button onclick="updateQty(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:bg-slate-200 rounded-r-lg transition"><i class="fas fa-plus text-[10px]"></i></button>
                        </div>
                        <button onclick="removeFromCart(${index})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            `);
        });

        $('#cartTotalDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(cartTotal));
    }

    function updateQty(index, change) {
        const item = cart[index];
        const newQty = item.qty + change;
        
        if (newQty > 0 && newQty <= item.max) {
            item.qty = newQty;
            renderCart();
        } else if (newQty > item.max) {
            showToast('warning', 'Stok maksimum tercapai!');
        }
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if(cart.length === 0) return;
        
        Swal.fire({
            title: 'Kosongkan Keranjang?',
            text: "Semua barang akan dihapus dari keranjang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Kosongkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
            }
        });
    }

    // Modal Pembayaran Logic
    function showPaymentModal() {
        if(cart.length === 0) return;
        
        $('#modalTotalDisplay').text('Rp ' + new Intl.NumberFormat('id-ID').format(cartTotal));
        $('#inputBayar').val('');
        $('#kembalianDisplay').text('Rp 0');
        $('#kembalianDisplay').removeClass('text-red-500 text-green-600').addClass('text-slate-400');
        
        // Reset metode
        changeMethod('tunai');
        
        $('#paymentModal').removeClass('hidden');
        setTimeout(() => $('#inputBayar').focus(), 100);
    }

    function closePaymentModal() {
        $('#paymentModal').addClass('hidden');
    }

    function changeMethod(method) {
        // Reset styles
        $('.grid label').removeClass('border-2 border-primary-500 bg-primary-50 border-slate-200');
        $('.grid label').addClass('border border-slate-200');
        $('.grid i').removeClass('text-primary-600').addClass('text-slate-400');
        $('.grid span').removeClass('text-primary-700 font-bold').addClass('text-slate-600 font-medium');

        // Apply active styles
        const activeLbl = $(`input[value="${method}"]`).parent();
        activeLbl.removeClass('border border-slate-200').addClass('border-2 border-primary-500 bg-primary-50');
        activeLbl.find('i').removeClass('text-slate-400').addClass('text-primary-600');
        activeLbl.find('span').removeClass('text-slate-600 font-medium').addClass('text-primary-700 font-bold');
        
        $(`input[value="${method}"]`).prop('checked', true);

        if(method !== 'tunai') {
            setUangPas();
            $('#inputBayar').prop('readonly', true);
            $('#inputBayar').addClass('bg-slate-100');
        } else {
            $('#inputBayar').prop('readonly', false);
            $('#inputBayar').removeClass('bg-slate-100');
            $('#inputBayar').val('');
            calculateKembalian();
        }
    }

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        if(value !== '') {
            input.value = new Intl.NumberFormat('id-ID').format(value);
        }
    }

    function getBayarValue() {
        return parseInt($('#inputBayar').val().replace(/\D/g, '')) || 0;
    }

    function setUangPas() {
        $('#inputBayar').val(new Intl.NumberFormat('id-ID').format(cartTotal));
        calculateKembalian();
    }

    function addBayar(amount) {
        let current = getBayarValue();
        $('#inputBayar').val(new Intl.NumberFormat('id-ID').format(current + amount));
        calculateKembalian();
    }

    function clearBayar() {
        $('#inputBayar').val('');
        calculateKembalian();
    }

    function calculateKembalian() {
        const bayar = getBayarValue();
        const kembalian = bayar - cartTotal;
        const display = $('#kembalianDisplay');

        if (bayar === 0) {
            display.text('Rp 0').removeClass('text-red-500 text-green-600').addClass('text-slate-400');
        } else if (kembalian < 0) {
            display.text('Kurang Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(kembalian)))
                   .removeClass('text-slate-400 text-green-600').addClass('text-red-500');
        } else {
            display.text('Rp ' + new Intl.NumberFormat('id-ID').format(kembalian))
                   .removeClass('text-slate-400 text-red-500').addClass('text-green-600');
        }
    }

    function prosesPenjualan() {
        const bayar = getBayarValue();
        const metode = $('input[name="metode"]:checked').val();

        if (bayar < cartTotal) {
            Swal.fire({icon: 'error', title: 'Uang Kurang!', text: 'Jumlah pembayaran kurang dari total tagihan.'});
            return;
        }

        const data = {
            items: cart.map(item => ({ barang_id: item.id, jumlah: item.qty })),
            bayar: bayar,
            metode_pembayaran: metode,
            anggota_id: $('#anggota_id').val()
        };

        const btn = $('#btnProses');
        const originalText = btn.html();
        btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...').prop('disabled', true);

        $.ajax({
            url: "{{ route('admin.penjualan.store') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    closePaymentModal();
                    
                    Swal.fire({
                        title: 'Transaksi Berhasil!',
                        html: `
                            <p class="mb-4">Kembalian:</p>
                            <h2 class="text-4xl font-bold text-green-600 mb-4">Rp ${new Intl.NumberFormat('id-ID').format(response.kembalian)}</h2>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Tutup & Lanjut Kasir',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload(); // Reload to refresh stock
                    });
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    }
</script>
@endpush

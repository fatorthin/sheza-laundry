@extends('layouts.admin')
@section('title', 'Kelola Layanan')
@section('content')
    @php
        $iconOptions = [
            'local_laundry_service',
            'iron',
            'checkroom',
            'dry_cleaning',
            'bed',
            'cleaning_services',
            'self_improvement',
            'hotel',
            'wash',
            'science',
            'tune',
            'inventory_2',
        ];
    @endphp
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold">Layanan</h1>
                <p class="text-sm text-on-surface-variant">Kelola daftar layanan laundry</p>
            </div>
            <button
                onclick="document.getElementById('modal-add-svc').classList.remove('hidden'); document.getElementById('modal-add-svc').classList.add('flex')"
                class="flex items-center gap-1 px-4 py-2 bg-primary-container text-white rounded-xl text-sm font-medium hover:bg-[#e08e0b]">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah
            </button>
        </div>

        <div class="space-y-3">
            @foreach ($services->groupBy('category') as $cat => $items)
                <div class="bg-white rounded-2xl border border-outline-variant overflow-hidden">
                    <div class="px-4 py-2 bg-surface-container border-b border-outline-variant">
                        <span class="text-xs font-bold uppercase tracking-wide text-[#865300]">{{ ucfirst($cat) }}</span>
                    </div>
                    @foreach ($items as $svc)
                        <div
                            class="flex items-center gap-3 px-4 py-3 border-b border-[#f0e0d2] last:border-0 hover:bg-surface-container/50">
                            <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center shrink-0">
                                <span
                                    class="material-symbols-outlined text-primary-container text-[18px] filled">{{ $svc->icon }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">{{ $svc->name }}</p>
                                <p class="text-xs text-on-surface-variant">Rp
                                    {{ number_format($svc->price, 0, ',', '.') }}/{{ $svc->unit }} · {{ $svc->type }}
                                </p>
                            </div>
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $svc->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $svc->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                            <button type="button" data-id="{{ $svc->id }}" data-name="{{ $svc->name }}"
                                data-category="{{ $svc->category }}" data-type="{{ $svc->type }}"
                                data-price="{{ (float) $svc->price }}" data-unit="{{ $svc->unit }}"
                                data-icon="{{ $svc->icon }}" data-description="{{ $svc->description }}"
                                data-sort-order="{{ $svc->sort_order ?? 0 }}"
                                data-is-active="{{ $svc->is_active ? '1' : '0' }}" onclick="openEditService(this)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-[#865300] transition-colors">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <form method="POST" action="{{ route('admin.services.destroy', $svc) }}"
                                onsubmit="return confirm('Hapus layanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="modal-add-svc" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-lg">Tambah Layanan</h3>
                <button type="button"
                    onclick="document.getElementById('modal-add-svc').classList.add('hidden'); document.getElementById('modal-add-svc').classList.remove('flex')"
                    class="text-on-surface-variant hover:text-red-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Layanan *</label>
                        <input type="text" name="name" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Kategori *</label>
                        <select name="category" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="sepatu">Sepatu</option>
                            <option value="setrika">Setrika</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Tipe *</label>
                        <select name="type" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Harga (Rp) *</label>
                        <input type="number" name="price" min="0" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Satuan *</label>
                        <input type="text" name="unit" value="pcs" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div class="col-span-2">
                        <input type="hidden" id="add-svc-icon" name="icon" value="local_laundry_service">
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Pilih Icon</label>
                        <div
                            class="flex flex-wrap gap-2 p-2 border border-outline-variant rounded-xl bg-[#fff8f4] max-h-40 overflow-y-auto">
                            @foreach ($iconOptions as $icon)
                                <button type="button" data-value="{{ $icon }}"
                                    onclick="selectServiceIcon(this, 'add')"
                                    style="width:2.5rem;height:2.5rem;flex:0 0 auto;"
                                    class="svc-icon-option add-svc-icon-option {{ $loop->first ? 'ring-2 ring-primary-container bg-surface-container' : '' }} rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-[#865300] filled">{{ $icon }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('modal-add-svc').classList.add('hidden'); document.getElementById('modal-add-svc').classList.remove('flex')"
                        class="flex-1 py-2.5 border border-outline-variant rounded-xl text-sm text-on-surface-variant hover:bg-surface-container">Batal</button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-primary-container text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="modal-edit-svc" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-lg">Edit Layanan</h3>
                <button type="button"
                    onclick="document.getElementById('modal-edit-svc').classList.add('hidden'); document.getElementById('modal-edit-svc').classList.remove('flex')"
                    class="text-on-surface-variant hover:text-red-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="form-edit-svc" method="POST" action="#" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Nama Layanan *</label>
                        <input id="edit-svc-name" type="text" name="name" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Kategori *</label>
                        <select id="edit-svc-category" name="category" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="sepatu">Sepatu</option>
                            <option value="setrika">Setrika</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Tipe *</label>
                        <select id="edit-svc-type" name="type" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Harga (Rp) *</label>
                        <input id="edit-svc-price" type="number" name="price" min="0" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Satuan *</label>
                        <input id="edit-svc-unit" type="text" name="unit" required
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div class="col-span-2">
                        <input type="hidden" id="edit-svc-icon" name="icon">
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Pilih Icon</label>
                        <div
                            class="flex flex-wrap gap-2 p-2 border border-outline-variant rounded-xl bg-[#fff8f4] max-h-40 overflow-y-auto">
                            @foreach ($iconOptions as $icon)
                                <button type="button" data-value="{{ $icon }}"
                                    onclick="selectServiceIcon(this, 'edit')"
                                    style="width:2.5rem;height:2.5rem;flex:0 0 auto;"
                                    class="svc-icon-option edit-svc-icon-option rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors">
                                    <span
                                        class="material-symbols-outlined text-[18px] text-[#865300] filled">{{ $icon }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Deskripsi</label>
                        <textarea id="edit-svc-description" name="description" rows="2"
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1">Urutan</label>
                        <input id="edit-svc-sort-order" type="number" name="sort_order" min="0"
                            class="w-full border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-container">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-on-surface-variant">
                            <input id="edit-svc-is-active" type="checkbox" name="is_active" value="1"
                                class="rounded border-outline-variant text-primary-container focus:ring-primary-container">
                            Aktif
                        </label>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button"
                        onclick="document.getElementById('modal-edit-svc').classList.add('hidden'); document.getElementById('modal-edit-svc').classList.remove('flex')"
                        class="flex-1 py-2.5 border border-outline-variant rounded-xl text-sm text-on-surface-variant hover:bg-surface-container">Batal</button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-primary-container text-white rounded-xl text-sm font-semibold hover:bg-[#e08e0b]">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function selectServiceIcon(button, mode) {
            const selector = mode === 'edit' ? '.edit-svc-icon-option' : '.add-svc-icon-option';
            document.querySelectorAll(selector).forEach((el) => {
                el.classList.remove('ring-2', 'ring-primary-container', 'bg-surface-container');
            });

            button.classList.add('ring-2', 'ring-primary-container', 'bg-surface-container');

            const iconValue = button.dataset.value;
            const targetInput = mode === 'edit' ? 'edit-svc-icon' : 'add-svc-icon';
            document.getElementById(targetInput).value = iconValue;
        }

        function openEditService(button) {
            const d = button.dataset;
            document.getElementById('form-edit-svc').action = `{{ url('/admin/services') }}/${d.id}`;
            document.getElementById('edit-svc-name').value = d.name || '';
            document.getElementById('edit-svc-category').value = d.category || 'kiloan';
            document.getElementById('edit-svc-type').value = d.type || 'satuan';
            document.getElementById('edit-svc-price').value = d.price || 0;
            document.getElementById('edit-svc-unit').value = d.unit || '';
            document.getElementById('edit-svc-icon').value = d.icon || 'local_laundry_service';
            document.getElementById('edit-svc-description').value = d.description || '';
            document.getElementById('edit-svc-sort-order').value = d.sortOrder || 0;
            document.getElementById('edit-svc-is-active').checked = d.isActive === '1';
            const activeEditIcon = document.querySelector(
                    `.edit-svc-icon-option[data-value="${document.getElementById('edit-svc-icon').value}"]`) ||
                document.querySelector('.edit-svc-icon-option');
            if (activeEditIcon) {
                selectServiceIcon(activeEditIcon, 'edit');
            }
            document.getElementById('modal-edit-svc').classList.remove('hidden');
            document.getElementById('modal-edit-svc').classList.add('flex');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const defaultAddIcon = document.querySelector(
                    '.add-svc-icon-option[data-value="local_laundry_service"]') ||
                document.querySelector('.add-svc-icon-option');
            if (defaultAddIcon) {
                selectServiceIcon(defaultAddIcon, 'add');
            }
        });
    </script>
@endsection

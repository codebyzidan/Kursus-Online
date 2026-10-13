<x-layouts.app title="Pengaturan situs">
    <x-ui.page-header
        title="Pengaturan situs"
        description="Nama situs, kontak, dan kebijakan keuangan."
        :items="['Dashboard admin' => route('admin.dashboard'), 'Pengaturan' => null]"
    />

    <form method="POST" action="{{ route('admin.settings.update') }}" class="card">
        <section class="grid gap-5 p-5">
            @csrf
            @method('PATCH')

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="site_name" label="Nama situs" :value="old('site_name', $settings->get('site_name')?->value)" required />
                <x-ui.input name="site_tagline" label="Tagline" :value="old('site_tagline', $settings->get('site_tagline')?->value)" />
                <x-ui.input name="contact_email" type="email" label="Email kontak" :value="old('contact_email', $settings->get('contact_email')?->value)" required />
                <x-ui.input name="contact_phone" label="Telepon" :value="old('contact_phone', $settings->get('contact_phone')?->value)" />
                <x-ui.input name="address" label="Alamat" :value="old('address', $settings->get('address')?->value)" />
                <x-ui.input name="platform_fee_percent" type="number" label="Biaya platform (%)" :value="old('platform_fee_percent', $settings->get('platform_fee_percent')?->value)" min="0" max="100" required />
            </div>

            <x-ui.textarea name="refund_policy" label="Kebijakan pengembalian dana" rows="4">{{ old('refund_policy', $settings->get('refund_policy')?->value) }}</x-ui.textarea>

            <x-ui.button type="submit" class="w-fit">Simpan pengaturan</x-ui.button>
        </section>
    </form>

    <x-ui.alert variant="info" title="Kredensial gateway">
        Kredensial Midtrans dan Google diatur melalui berkas <code>.env</code> agar tidak tersimpan di basis data.
    </x-ui.alert>
</x-layouts.app>

@extends('layouts.admin', ['title' => 'SalesPro', 'pageTitle' => 'API Integrations'])

@section('content')
    <section class="content">
        <div class="page-header">
            <div>
                <h2>E-commerce Integration API</h2>
                <p>Expose live product inventory to third-party e-commerce platforms with secure bearer tokens.</p>
            </div>
        </div>

        <div class="api-grid">
            <section class="panel api-card">
                <div class="api-card-icon blue">
                    @include('admin.partials.nav-icon', ['icon' => 'api'])
                </div>
                <div>
                    <span class="api-label">Base URL</span>
                    <strong>{{ $baseUrl ?: request()->getSchemeAndHttpHost() }}/api/v1</strong>
                    <small>Use this prefix for every integration request.</small>
                </div>
            </section>

            <section class="panel api-card">
                <div class="api-card-icon green">
                    @include('admin.partials.nav-icon', ['icon' => 'box'])
                </div>
                <div>
                    <span class="api-label">Product Scope</span>
                    <strong>Active products only</strong>
                    <small>SKU, product name, price and available stock are exposed.</small>
                </div>
            </section>

            <section class="panel api-card">
                <div class="api-card-icon purple">
                    @include('admin.partials.nav-icon', ['icon' => 'settings'])
                </div>
                <div>
                    <span class="api-label">Auth Header</span>
                    <strong>Bearer token</strong>
                    <small>Tokens are stored as hashes in the database.</small>
                </div>
            </section>
        </div>

        <section class="panel api-note">
            <div class="panel-header">
                <h2>How This Integration Works</h2>
            </div>

            <div class="api-note-grid">
                <div>
                    <strong>1. Client Access</strong>
                    <p>Each external e-commerce platform gets its own API client token. The raw token is shown only when generated, while the database stores only a hashed token.</p>
                </div>
                <div>
                    <strong>2. Secure Request</strong>
                    <p>The third-party system sends requests with <code>Authorization: Bearer token</code>. Invalid or inactive tokens are rejected before product data is returned.</p>
                </div>
                <div>
                    <strong>3. Product Sync</strong>
                    <p>The API returns only active product inventory needed for integration: SKU, product name, price and available stock.</p>
                </div>
                <div>
                    <strong>4. Tracking</strong>
                    <p>When a token is used successfully, the client record updates its last used time so admins can monitor integration activity.</p>
                </div>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h2>API Clients</h2>
                <code class="inline-code">php artisan salespro:api-token "Client Name"</code>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Client Name</th>
                            <th>Token</th>
                            <th>Status</th>
                            <th>Last Used</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>{{ $clients->firstItem() + $loop->index }}</td>
                                <td>{{ $client->name }}</td>
                                <td><code class="inline-code">{{ str($client->token_hash)->limit(18, '...') }}</code></td>
                                <td><span @class(['status', 'inactive' => ! $client->is_active])>{{ $client->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ $client->last_used_at ? $client->last_used_at->format('M d, Y h:i A') : 'Never' }}</td>
                                <td>{{ $client->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">No API clients found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($clients->hasPages())
                <div class="pagination">{{ $clients->links() }}</div>
            @endif
        </section>

        <div class="api-doc-grid">
            <section class="panel">
                <div class="panel-header">
                    <h2>Endpoints</h2>
                </div>
                <div class="endpoint-list">
                    <div class="endpoint-row">
                        <span>GET</span>
                        <code>/api/v1/products</code>
                    </div>
                    <div class="endpoint-row">
                        <span>GET</span>
                        <code>/api/v1/products/{sku}</code>
                    </div>
                    <div class="endpoint-row">
                        <span>GET</span>
                        <code>/api/v1/products?search=iphone</code>
                    </div>
                    <div class="endpoint-row">
                        <span>GET</span>
                        <code>/api/v1/products?sku=IP15PRO</code>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2>Example Request</h2>
                </div>
                <pre class="code-block">curl.exe -H "Authorization: Bearer {{ $demoToken }}" ^
  -H "Accept: application/json" ^
  "{{ $baseUrl ?: request()->getSchemeAndHttpHost() }}/api/v1/products"</pre>
            </section>
        </div>

        <section class="panel">
            <div class="panel-header">
                <h2>Example Response</h2>
            </div>
            <pre class="code-block">{
  "data": [
    {
      "sku": "IP15PRO",
      "product_name": "iPhone 15 Pro",
      "price": 135000,
      "available_stock": 24
    }
  ]
}</pre>
        </section>
    </section>
@endsection

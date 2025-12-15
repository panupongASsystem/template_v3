<!DOCTYPE html>
<html>
<head>
    <title>Chat Configuration Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-chat-dots"></i> Chat System Configuration</h2>
                    <div>
                        <a href="<?= site_url('chat_backend/stats') ?>" class="btn btn-info me-2">
                            <i class="bi bi-graph-up"></i> Statistics
                        </a>
                        <a href="<?= site_url('chat/test_gemini') ?>" target="_blank" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Test API
                        </a>
                    </div>
                </div>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $this->session->flashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $this->session->flashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Configs</h6>
                                        <h3><?= count($configs) ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-gear-fill fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Active Configs</h6>
                                        <h3><?= count(array_filter($configs, function($c) { return $c['is_active']; })) ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle-fill fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Conversations (7d)</h6>
                                        <h3><?= array_sum(array_column($stats, 'total_conversations')) ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-chat-left-text-fill fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Unique Users (7d)</h6>
                                        <h3><?= array_sum(array_column($stats, 'unique_users')) ?></h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people-fill fs-2"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cleanup Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-trash"></i> Data Cleanup</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= site_url('chat_backend/cleanup') ?>" class="d-inline">
                            <div class="input-group" style="max-width: 300px;">
                                <input type="number" class="form-control" name="days" value="30" min="1" max="365">
                                <span class="input-group-text">days</span>
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i> Clean Old Data
                                </button>
                            </div>
                        </form>
                        <small class="text-muted">Remove rate limits and logs older than specified days</small>
                    </div>
                </div>

                <!-- Configuration Table -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-list-ul"></i> Configuration Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Config Name</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($configs as $config): ?>
                                        <tr>
                                            <td><code><?= html_escape($config['config_name']) ?></code></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= html_escape($config['config_type']) ?></span>
                                            </td>
                                            <td>
                                                <div class="config-value" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                    <?php if ($config['config_type'] === 'json'): ?>
                                                        <code><?= html_escape(substr($config['config_value'], 0, 50)) . '...' ?></code>
                                                    <?php elseif (strlen($config['config_value']) > 50): ?>
                                                        <?= html_escape(substr($config['config_value'], 0, 50)) . '...' ?>
                                                    <?php else: ?>
                                                        <?= html_escape($config['config_value']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= html_escape($config['description']) ?></td>
                                            <td>
                                                <?php if ($config['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        onclick="editConfig(<?= html_escape(json_encode($config)) ?>)">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                
                                                <?php if ($config['is_active']): ?>
                                                    <a href="<?= site_url('chat_backend/toggle_config/' . urlencode($config['config_name']) . '/0') ?>" 
                                                       class="btn btn-sm btn-warning"
                                                       onclick="return confirm('Disable this config?')">
                                                        <i class="bi bi-pause-circle"></i> Disable
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= site_url('chat_backend/toggle_config/' . urlencode($config['config_name']) . '/1') ?>" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="bi bi-play-circle"></i> Enable
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="<?= site_url('chat_backend/update_config') ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Configuration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Config Name</label>
                            <input type="text" class="form-control" name="config_name" id="edit_config_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="config_type" id="edit_config_type">
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <textarea class="form-control" name="config_value" id="edit_config_value" rows="5" required></textarea>
                            <div class="form-text">For JSON type, ensure proper JSON format</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editConfig(config) {
            document.getElementById('edit_config_name').value = config.config_name;
            document.getElementById('edit_config_type').value = config.config_type;
            document.getElementById('edit_config_value').value = config.config_value;
            document.getElementById('edit_description').value = config.description || '';
        }

        // Auto-resize textarea
        document.getElementById('edit_config_value').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>
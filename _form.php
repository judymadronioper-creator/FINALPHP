<?php
// Shared form used by add.php AND edit.php
// Needs these variables: $customer (array), $errors (array), $button_label (string)
?>
<div class="glass p-4">

  <?php if (count($errors) > 0): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Full name</label>
        <input type="text" name="full_name" class="form-control" value="<?= e($customer["full_name"]) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="<?= e($customer["phone"]) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Email <span class="text-secondary">(optional)</span></label>
        <input type="email" name="email" class="form-control" value="<?= e($customer["email"]) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Address <span class="text-secondary">(optional)</span></label>
        <input type="text" name="address" class="form-control" value="<?= e($customer["address"]) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Customer type</label>
        <select name="customer_type" class="form-select">
          <?php foreach (customer_types() as $type): ?>
            <option value="<?= e($type) ?>" <?= ($customer["customer_type"] == $type) ? "selected" : "" ?>><?= e($type) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <?php foreach (customer_statuses() as $status): ?>
            <option value="<?= e($status) ?>" <?= ($customer["status"] == $status) ? "selected" : "" ?>><?= e($status) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-gradient"><?= e($button_label) ?></button>
      <a href="index.php" class="btn btn-link text-secondary">Cancel</a>
    </div>
  </form>
</div>

<div class="search-container">
    <h2 class="centered-title">Hae Tunteja</h2>
    <div class="yellow-lines">
        <div class="yellow-line1"></div>
        <div class="yellow-line2"></div>
    </div>
    <div class="dropdown">
        <label>Kaupunki</label>
        <select id="citySelect">
            <option value=""></option>
            <?php foreach ($cityResult as $row) : ?>
                <option value="<?= htmlspecialchars($row['city']) ?>"><?= htmlspecialchars($row['city']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="dropdown">
        <label>Kuntosalin Osoite</label>
        <select id="gymSelect">
            <option value=""></option>
            <?php foreach ($addressResult as $row) : ?>
                <option value="<?= htmlspecialchars($row['address']) ?>"><?= htmlspecialchars($row['address']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <h2 class="centered-title_filters-title">Suodattimet</h2>
    <div class="dropdown">
        <label>Tunnin Nimi</label>
        <select id="classNameSelect">
            <option value=""></option>
            <?php foreach ($classNameResult as $row) : ?>
                <option value="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="dropdown">
        <label>Ohjaajat</label>
        <select id="instructorSelect">
            <option value=""></option>
            <?php foreach ($instructorResult as $row) : ?>
                <option value="<?= htmlspecialchars($row['instructor_id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="time-filter">
        <label>Alkuaika:</label>
        <input type="time" id="startTime">
    </div>
    <div class="time-filter">
        <label>Loppuaika:</label>
        <input type="time" id="endTime">
    </div>
</div>
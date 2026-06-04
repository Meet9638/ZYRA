@extends('layouts.app')

@section('title', 'Size Guide - ZYRA')

@section('content')
<div class="container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="{{ route('home') }}" class="breadcrumb-link">Home</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Size Guide</span>
    </div>

    <!-- Page Header -->
    <div class="size-guide-header">
        <h1>Size Guide</h1>
        <p>Find your perfect fit with our comprehensive size guide. Our AI-powered recommendations ensure you get the right size every time.</p>
    </div>

    <!-- Size Guide Content -->
    <div class="size-guide-content">
        <!-- Men's Sizes -->
        <section class="size-section">
            <h2>Men's Sizes</h2>
            <div class="size-table-container">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Chest (in)</th>
                            <th>Waist (in)</th>
                            <th>Hips (in)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="size-value">S</td>
                            <td>34 - 36</td>
                            <td>28 - 30</td>
                            <td>35 - 37</td>
                        </tr>
                        <tr>
                            <td class="size-value">M</td>
                            <td>38 - 40</td>
                            <td>31 - 33</td>
                            <td>39 - 41</td>
                        </tr>
                        <tr>
                            <td class="size-value">L</td>
                            <td>42 - 44</td>
                            <td>34 - 36</td>
                            <td>43 - 46</td>
                        </tr>
                        <tr>
                            <td class="size-value">XL</td>
                            <td>46 - 48</td>
                            <td>37 - 39</td>
                            <td>47 - 50</td>
                        </tr>
                        <tr>
                            <td class="size-value">XXL</td>
                            <td>50 - 52</td>
                            <td>40 - 42</td>
                            <td>51 - 54</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Women's Sizes -->
        <section class="size-section">
            <h2>Women's Sizes</h2>
            <div class="size-table-container">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Bust (in)</th>
                            <th>Waist (in)</th>
                            <th>Hips (in)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="size-value">XS</td>
                            <td>30 - 32</td>
                            <td>22 - 24</td>
                            <td>32 - 34</td>
                        </tr>
                        <tr>
                            <td class="size-value">S</td>
                            <td>33 - 35</td>
                            <td>25 - 27</td>
                            <td>35 - 37</td>
                        </tr>
                        <tr>
                            <td class="size-value">M</td>
                            <td>36 - 38</td>
                            <td>28 - 30</td>
                            <td>38 - 40</td>
                        </tr>
                        <tr>
                            <td class="size-value">L</td>
                            <td>39 - 41</td>
                            <td>31 - 33</td>
                            <td>41 - 43</td>
                        </tr>
                        <tr>
                            <td class="size-value">XL</td>
                            <td>42 - 44</td>
                            <td>34 - 36</td>
                            <td>44 - 46</td>
                        </tr>
                        <tr>
                            <td class="size-value">XXL</td>
                            <td>45 - 47</td>
                            <td>37 - 39</td>
                            <td>47 - 49</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Unisex Sizes -->
        <section class="size-section">
            <h2>Unisex Sizes</h2>
            <div class="size-table-container">
                <table class="size-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Chest/Bust (in)</th>
                            <th>Waist (in)</th>
                            <th>Hips (in)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="size-value">S</td>
                            <td>34 - 37</td>
                            <td>27 - 30</td>
                            <td>35 - 38</td>
                        </tr>
                        <tr>
                            <td class="size-value">M</td>
                            <td>38 - 41</td>
                            <td>31 - 34</td>
                            <td>39 - 42</td>
                        </tr>
                        <tr>
                            <td class="size-value">L</td>
                            <td>42 - 45</td>
                            <td>35 - 38</td>
                            <td>43 - 46</td>
                        </tr>
                        <tr>
                            <td class="size-value">XL</td>
                            <td>46 - 49</td>
                            <td>39 - 42</td>
                            <td>47 - 50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- AI Size Recommendations -->
        <section class="ai-recommendations">
            <div class="ai-card">
                <div class="ai-icon">🧠</div>
                <div class="ai-content">
                    <h3>AI-Powered Size Recommendations</h3>
                    <p>Our advanced AI analyzes your purchase history, returns, and preferences to recommend the perfect size for you. Simply create an account and let our technology work for you!</p>
                    <div class="ai-features">
                        <div class="ai-feature">
                            <span class="feature-icon">📊</span>
                            <span>Analyzes 2M+ data points</span>
                        </div>
                        <div class="ai-feature">
                            <span class="feature-icon">🎯</span>
                            <span>94% accuracy rate</span>
                        </div>
                        <div class="ai-feature">
                            <span class="feature-icon">🔄</span>
                            <span>Learns from your feedback</span>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" class="ai-cta">Get Personalized Recommendations</a>
                </div>
            </div>
        </section>

        <!-- Measuring Tips -->
        <section class="measuring-tips">
            <h2>How to Measure</h2>
            <div class="tips-grid">
                <div class="tip-card">
                    <div class="tip-icon">📏</div>
                    <h3>Chest/Bust</h3>
                    <p>Measure around the fullest part of your chest/bust, keeping the tape measure parallel to the floor.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">⭕</div>
                    <h3>Waist</h3>
                    <p>Measure around your natural waistline, which is typically the narrowest part of your torso.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">📐</div>
                    <h3>Hips</h3>
                    <p>Measure around the fullest part of your hips, keeping your feet together.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">💡</div>
                    <h3>Pro Tips</h3>
                    <ul>
                        <li>Use a flexible measuring tape</li>
                        <li>Measure over lightweight clothing</li>
                        <li>Keep the tape snug but not tight</li>
                        <li>Take measurements twice for accuracy</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add smooth scrolling to size tables
    document.querySelectorAll('.size-table').forEach(table => {
        table.addEventListener('wheel', function(e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                this.scrollLeft += e.deltaY;
            }
        });
    });
</script>
@endpush


@extends('layouts.app')

@section('title', 'Contact Us - ZYRA')

@section('content')
<div class="hero" style="min-height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, var(--zyra-black), var(--zyra-charcoal)); border-bottom: 1px solid var(--border-color);">
    <div>
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem; color: var(--zyra-gold);">Get in Touch</h1>
        <p style="font-size: 1.2rem; color: var(--zyra-silver); max-width: 800px; margin: 0 auto;">Our dedicated concierge team is ready to assist you.</p>
    </div>
</div>

<div class="container" style="padding: 4rem 2rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem;">
        
        <!-- Contact Form -->
        <div class="product-card" style="padding: 3rem 2rem; border-top: 4px solid var(--zyra-gold); cursor: default;">
            <h2 style="font-size: 2rem; color: var(--zyra-white); margin-bottom: 1.5rem;">Send a Message</h2>
            
            <form action="#" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf
                
                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Jane Doe" required>
                </div>
                
                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="jane@example.com" required>
                </div>
                
                <div>
                    <label for="subject" class="form-label">Inquiry Type</label>
                    <select id="subject" name="subject" class="form-control" required style="cursor: pointer;">
                        <option value="">Select a topic</option>
                        <option value="order">Order Status</option>
                        <option value="returns">Returns & Exchanges</option>
                        <option value="styling">Personal Styling Advice</option>
                        <option value="other">Other Inquiry</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="form-label">Message</label>
                    <textarea id="message" name="message" class="form-control" rows="5" placeholder="How can we help you today?" required></textarea>
                </div>
                
                <button type="button" class="btn-primary" style="width: 100%; margin-top: 1rem;">Send Message</button>
            </form>
        </div>

        <!-- Contact Information -->
        <div>
            <h2 style="font-size: 2.5rem; color: var(--zyra-gold); margin-bottom: 1.5rem;">The NOIR Concierge</h2>
            <p style="color: var(--zyra-silver); font-size: 1.1rem; line-height: 1.8; margin-bottom: 3rem;">
                Whether you need assistance with an existing order, require personalized styling advice from our AI specialists, or simply have a question about our luxury collections, we are here to provide an unparalleled service experience.
            </p>

            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 50px; height: 50px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid var(--zyra-gold);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--zyra-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: var(--zyra-white); font-size: 1.2rem; margin-bottom: 0.3rem;">Phone</h3>
                        <p style="color: var(--muted-text);">+1 (800) 555-NOIR</p>
                        <p style="color: var(--zyra-silver); font-size: 0.85rem; margin-top: 0.2rem;">Available Mon-Fri, 9am - 8pm EST</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 50px; height: 50px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid var(--zyra-gold);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--zyra-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: var(--zyra-white); font-size: 1.2rem; margin-bottom: 0.3rem;">Email</h3>
                        <p style="color: var(--muted-text);">concierge@zyra-ai.com</p>
                        <p style="color: var(--zyra-silver); font-size: 0.85rem; margin-top: 0.2rem;">We aim to respond within 2 hours</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 50px; height: 50px; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid var(--zyra-gold);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--zyra-gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color: var(--zyra-white); font-size: 1.2rem; margin-bottom: 0.3rem;">Headquarters</h3>
                        <p style="color: var(--muted-text);">1 Luxury Avenue, Suite 100</p>
                        <p style="color: var(--muted-text);">New York, NY 10022</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


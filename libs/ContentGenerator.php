<?php
/**
 * Content Generator - Fetches trending content from various sources
 */

class ContentGenerator {
    private $db;
    private $sources = [];

    public function __construct($database = null) {
        $this->db = $database;
    }

    /**
     * Generate content based on category
     * @param string $category - Content category
     * @return array - Generated content
     */
    public function generateContent($category) {
        $methodMap = [
            'tech_ai' => 'getTechAiUpdates',
            'funny_memes' => 'getFunnyVideosMemes',
            'relationship_stories' => 'getRelationshipStories',
            'motivational_content' => 'getMotivationalContent',
            'news_trending' => 'getNewsTrendingIssues',
            'football_highlights' => 'getFootballHighlights',
            'health_tips' => 'getHealthTips'
        ];
        
        $method = $methodMap[$category] ?? null;
        
        if ($method && method_exists($this, $method)) {
            return $this->$method();
        }
        
        return null;
    }

    /**
     * Get Tech & AI updates
     */
    private function getTechAiUpdates() {
        $techUpdates = [
            "🤖 Latest AI breakthroughs are changing the tech industry faster than ever! What's your favorite AI tool? #AI #Technology",
            "💡 Did you know? Machine learning algorithms now power most of today's innovations. Stay updated with the latest tech trends!",
            "🚀 New AI model released today with capabilities beyond what we imagined. The future is here! #TechNews #Innovation",
            "🔬 Quantum computing is making progress. Scientists announce new breakthrough in quantum processor capabilities #TechTalk",
            "💻 Python remains the most popular programming language for AI development. Are you learning AI?",
            "🌐 Cloud technology is revolutionizing how businesses operate. Join the digital transformation #CloudTech",
            "📱 5G technology deployment accelerates worldwide. What does this mean for your devices? #5G #Future"
        ];

        return [
            'title' => $techUpdates[array_rand($techUpdates)],
            'content' => 'Check out the latest in tech and AI innovations',
            'category' => 'tech_ai',
            'link' => 'https://www.techcrunch.com/latest', // Update with your preferred source
            'image' => $this->getTechImage()
        ];
    }

    /**
     * Get Funny videos/memes
     */
    private function getFunnyVideosMemes() {
        $memes = [
            "😂 POV: When someone says they don't use memes. #MemeLife #FunnyMemes",
            "🤣 That moment when... LOL! Tag someone who'd do this! #Viral #Funny",
            "😆 Can't stop laughing at this! Who else? 👇 #MemeOfTheDay",
            "🎬 The funniest video on the internet right now! Watch till the end 😂 #Viral",
            "😹 When life gives you lemons... just laugh! #Comedy #FunnyContent",
            "🎉 POV: Monday morning vs Friday afternoon 😆 #WeeklyLaugh",
            "😂 Try not to laugh challenge! Can you make it to the end? #Challenge"
        ];

        return [
            'title' => $memes[array_rand($memes)],
            'content' => 'Check out these hilarious videos and memes!',
            'category' => 'funny_memes',
            'link' => '', // Add your meme/video source if available
            'image' => $this->getFunnyImage()
        ];
    }

    /**
     * Get Relationship stories
     */
    private function getRelationshipStories() {
        $stories = [
            "💑 True love doesn't have a deadline. It's a journey we take with our person. What's your love story? #RelationshipGoals #Love",
            "❤️ The best relationships are the ones where you can be yourself completely. Who's your person? #Soulmate #LoveWins",
            "💕 Love is not about finding someone you can live with. It's about finding someone you can't imagine living without. #RelationshipAdvice",
            "😍 Couples who support each other's dreams stay together. Build each other up! #CoupleGoals #Support",
            "🥰 Little moments together make the biggest memories. Cherish them! #RelationshipTips",
            "💞 Communication is the foundation of every healthy relationship. Talk it out! #HealthyRelationship",
            "✨ Love is patient, love is kind. Never settle for less! #LoveQuotes #Relationships"
        ];

        return [
            'title' => $stories[array_rand($stories)],
            'content' => 'Relationship inspiration and real stories',
            'category' => 'relationship_stories',
            'link' => '',
            'image' => $this->getRelationshipImage()
        ];
    }

    /**
     * Get Motivational content
     */
    private function getMotivationalContent() {
        $quotes = [
            "🌟 Your dreams are worth fighting for! Don't give up now. The best is yet to come. #Motivation #DreamBig",
            "💪 Success is not final, failure is not fatal. It's the courage to continue that counts. #Perseverance #NeverGiveUp",
            "🎯 You are capable of more than you know. Believe in yourself! #SelfBelief #Confidence",
            "🚀 Every expert was once a beginner. Keep pushing forward! #GrowthMindset #Learning",
            "✨ Your potential is limitless. Don't let anyone dim your light! #BeYourBest #Empowerment",
            "🏆 Progress over perfection. Celebrate every win, no matter how small! #WinTheDay #GrowthJourney",
            "❤️ You've got this! Don't quit on your dreams. The struggle makes the victory sweeter. #NeverSettle #Goals"
        ];

        return [
            'title' => $quotes[array_rand($quotes)],
            'content' => 'Daily dose of motivation to fuel your dreams',
            'category' => 'motivational_content',
            'link' => '',
            'image' => $this->getMotivationImage()
        ];
    }

    /**
     * Get News & trending issues
     */
    private function getNewsTrendingIssues() {
        $news = [
            "📰 Breaking News Update: Stay informed with the latest global developments. #News #Trending",
            "🌍 Global issue making headlines today: What's your take? #NewsAlert #CurrentEvents",
            "📢 Trending worldwide: Important updates you need to know about! #Viral #Trending",
            "🔔 Latest news cycle: What everyone is talking about right now. #NewsBreak #Headlines",
            "📺 Today's top story: Don't miss these important developments. #News #MustWatch",
            "🌐 International news: Important updates from around the world. #GlobalNews",
            "⚡ Flash news: Latest breaking updates delivered to your feed. #NewsAlert #BreakingNews"
        ];

        return [
            'title' => $news[array_rand($news)],
            'content' => 'Stay updated with trending news and important issues',
            'category' => 'news_trending',
            'link' => 'https://www.bbc.com/news', // Update with your news source
            'image' => $this->getNewsImage()
        ];
    }

    /**
     * Get Football highlights
     */
    private function getFootballHighlights() {
        $football = [
            "⚽ GOOOAL! Amazing highlights from today's match! Which team are you supporting? #Football #GoalAlert",
            "🏆 Match of the day recap: What a game! The drama, the goals, the celebration! #Football #Sports",
            "👟 Best football moments from this week! Did you catch these incredible plays? #Football #Highlights",
            "⚽ Your favorite team is on fire! Latest updates and highlights right here. #Football #HotTeam",
            "🎯 Top scorers update: Who will break the record this season? #Football #LeagueStandings",
            "💛 Football fans, this one's for you! Best saves and skills compilation. #Football #SkillCheck",
            "⚡ Derby day reaction: The intensity, the passion, the unforgettable moments! #Football #Derby"
        ];

        return [
            'title' => $football[array_rand($football)],
            'content' => 'Football highlights and match updates',
            'category' => 'football_highlights',
            'link' => 'https://www.espn.com/soccer/', // Update with your sports source
            'image' => $this->getFootballImage()
        ];
    }

    /**
     * Get Health tips
     */
    private function getHealthTips() {
        $health = [
            "🏃 Daily health tip: Move your body! Even 30 minutes of activity can transform your health. #HealthyLifestyle #Fitness",
            "🥗 Nutrition matters! Remember to eat colorful foods rich in vitamins and nutrients. #HealthyEating #Wellness",
            "💤 Sleep is crucial! Aim for 7-9 hours every night for better health and mood. #SleepWell #HealthTips",
            "💧 Stay hydrated! Drinking water is essential for every body function. #DrinkWater #HealthyHabits",
            "🧘 Mental health is health! Take time to meditate and reduce stress today. #MentalWellness #Mindfulness",
            "🚫 Say no to sugar! Limit sugary drinks and embrace natural sweetness. #HealthyChoices #Wellness",
            "🏋️ Strength training benefits: Build muscle, boost metabolism, improve confidence! #FitnessGoals #Training"
        ];

        return [
            'title' => $health[array_rand($health)],
            'content' => 'Daily health and wellness tips for a better life',
            'category' => 'health_tips',
            'link' => '',
            'image' => $this->getHealthImage()
        ];
    }

    /**
     * Get random image URL for each category
     */
    private function getTechImage() {
        $images = [
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getFunnyImage() {
        $images = [
            'https://images.unsplash.com/photo-1516239482537-e3b720bb3a4a?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1527482797697-8795b1a55a45?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getRelationshipImage() {
        $images = [
            'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getMotivationImage() {
        $images = [
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe3e?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getNewsImage() {
        $images = [
            'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1487180144351-b8472da7d491?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getFootballImage() {
        $images = [
            'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1517747745726-15c5e4b139b7?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    private function getHealthImage() {
        $images = [
            'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=600&h=400&fit=crop',
            'https://images.unsplash.com/photo-1526891929874-2cccc89daf60?w=600&h=400&fit=crop'
        ];
        return $images[array_rand($images)];
    }

    /**
     * All available categories
     */
    public static function getCategories() {
        return [
            'tech_ai',
            'funny_memes',
            'relationship_stories',
            'motivational_content',
            'news_trending',
            'football_highlights',
            'health_tips'
        ];
    }
}
?>

package com.example.ui.theme

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val DarkFantasyColorScheme = darkColorScheme(
    primary = GoldPrimary,
    onPrimary = DarkBackground,
    primaryContainer = GoldContainer,
    onPrimaryContainer = GoldLight,
    secondary = PurpleSecondary,
    onSecondary = Color.White,
    secondaryContainer = PurpleContainer,
    onSecondaryContainer = PurpleLight,
    tertiary = CombatRed,
    onTertiary = Color.White,
    tertiaryContainer = CombatRedContainer,
    onTertiaryContainer = Color(0xFFFEE2E2),
    background = DarkBackground,
    onBackground = TextPrimary,
    surface = DarkSurface,
    onSurface = TextPrimary,
    surfaceVariant = DarkSurfaceVariant,
    onSurfaceVariant = TextSecondary,
    outline = OutlineGold,
    outlineVariant = OutlineDark
)

@Composable
fun OBardoTheme(
    content: @Composable () -> Unit
) {
    MaterialTheme(
        colorScheme = DarkFantasyColorScheme,
        typography = Typography,
        content = content
    )
}


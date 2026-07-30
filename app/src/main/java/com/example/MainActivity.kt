package com.example

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.viewModels
import com.example.ui.BardoViewModel
import com.example.ui.navigation.BardoAppNavigation
import com.example.ui.theme.OBardoTheme

class MainActivity : ComponentActivity() {
    private val viewModel: BardoViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            OBardoTheme {
                BardoAppNavigation(viewModel = viewModel)
            }
        }
    }
}


package com.example.ai

import com.example.BuildConfig
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import org.json.JSONArray
import org.json.JSONObject
import java.util.concurrent.TimeUnit

class GeminiService {

    private val client = OkHttpClient.Builder()
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    suspend fun formatVoiceChronicle(rawTranscript: String, locationName: String): String = withContext(Dispatchers.IO) {
        val apiKey = try { BuildConfig.GEMINI_API_KEY } catch (e: Exception) { "" }
        if (apiKey.isBlank() || apiKey == "MY_GEMINI_API_KEY") {
            return@withContext "📜 **Crônica da Sessão em $locationName**:\n\n• $rawTranscript\n\n*(Transcrevendo eventos e atualizando diário da campanha)*"
        }

        val prompt = """
            Você é 'O Bardo', um assistente bardico narrador de RPG de Mesa.
            O Mestre gravou a seguinte nota por voz durante a sessão no local '$locationName':
            "$rawTranscript"
            
            Sua tarefa é formatar essa nota em uma entrada de diário/crônica de RPG imersiva em Português do Brasil.
            Use tópicos com marcadores (•), destaque nomes em negrito, adicione consequências para a história e segredos relevantes.
        """.trimIndent()

        callGeminiRestApi(prompt, apiKey)
    }

    suspend fun describeScene(locationName: String, parentLocation: String): String = withContext(Dispatchers.IO) {
        val apiKey = try { BuildConfig.GEMINI_API_KEY } catch (e: Exception) { "" }
        if (apiKey.isBlank() || apiKey == "MY_GEMINI_API_KEY") {
            return@withContext "Ao adentrar em $locationName, o ar fica pesado. Sombras dançam nas paredes de pedra enquanto o som distante do ambiente ecoa pelos corredores."
        }

        val prompt = """
            Você é um Mestre de RPG especialista em narração imersiva.
            Crie uma descrição sensorial da cena para ser lida em voz alta aos jogadores ao entrarem em '$locationName' (Localizado em '$parentLocation').
            Inclua detalhes visuais, auditivos, táteis e olfativos em até 3 parágrafos curtos e marcantes.
        """.trimIndent()

        callGeminiRestApi(prompt, apiKey)
    }

    suspend fun generateSecret(locationName: String): String = withContext(Dispatchers.IO) {
        val apiKey = try { BuildConfig.GEMINI_API_KEY } catch (e: Exception) { "" }
        if (apiKey.isBlank() || apiKey == "MY_GEMINI_API_KEY") {
            return@withContext "🔒 **Segredo Oculto**: Uma alavanca disfarçada atrás do candelabro revelará um compartimento secreto com um antigo diário."
        }

        val prompt = """
            Gere um segredo de RPG misterioso e uma pista associada para o local '$locationName'.
            Retorne em Português formatado com emojis.
        """.trimIndent()

        callGeminiRestApi(prompt, apiKey)
    }

    private fun callGeminiRestApi(prompt: String, apiKey: String): String {
        return try {
            val url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=$apiKey"

            val jsonBody = JSONObject().apply {
                put("contents", JSONArray().apply {
                    put(JSONObject().apply {
                        put("parts", JSONArray().apply {
                            put(JSONObject().put("text", prompt))
                        })
                    })
                })
            }

            val request = Request.Builder()
                .url(url)
                .post(jsonBody.toString().toRequestBody("application/json".toMediaType()))
                .build()

            val response = client.newCall(request).execute()
            val responseText = response.body?.string() ?: ""

            if (!response.isSuccessful) {
                return "📜 Resumo: $prompt"
            }

            val jsonResponse = JSONObject(responseText)
            val candidates = jsonResponse.optJSONArray("candidates")
            val firstCandidate = candidates?.optJSONObject(0)
            val content = firstCandidate?.optJSONObject("content")
            val parts = content?.optJSONArray("parts")
            val text = parts?.optJSONObject(0)?.optString("text")

            text ?: prompt
        } catch (e: Exception) {
            "📜 Registrado: $prompt"
        }
    }
}

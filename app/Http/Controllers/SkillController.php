<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::all();
        return response()->json($skills);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'skill_name' => 'required|string|max:255|unique:skills,skill_name',
            ]);

            $skill = Skill::create($request->all());
            return response()->json([
                'message' => 'Habilidade criada com sucesso!',
                'skill' => $skill
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar habilidade: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $skill = Skill::where('skill_id', $id)->first();

        if (!$skill) {
            return response()->json(['message' => 'Habilidade não encontrada.'], 404);
        }

        return response()->json($skill);
    }

    public function update(Request $request, string $id)
    {
        try {
            $skill = Skill::where('skill_id', $id)->first();

            if (!$skill) {
                return response()->json(['message' => 'Habilidade não encontrada.'], 404);
            }

            $request->validate([
                'skill_name' => 'required|string|max:255|unique:skills,skill_name,' . $id . ',skill_id',
            ]);

            $skill->update($request->all());

            return response()->json(['message' => 'Habilidade atualizada com sucesso!', 'skill' => $skill]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar habilidade: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $skill = Skill::where('skill_id', $id)->first();

            if (!$skill) {
                return response()->json(['message' => 'Habilidade não encontrada.'], 404);
            }

            $skill->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar habilidade: ' . $e->getMessage()], 500);
        }
    }
}
